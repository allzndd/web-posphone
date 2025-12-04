<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'items.product', 'payment']);

        // Apply search if provided
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                // Check for special keywords
                $lowerSearch = strtolower($search);

                if (in_array($lowerSearch, ['tradein', 'trade-in', 'trade in', 'tukar tambah'])) {
                    // Filter transactions with type trade-in
                    $q->where('type', 'trade-in');
                } else {
                    // Normal search by customer name, product name
                    $q->whereHas('customer', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('items.product', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('invoice_number', 'like', "%{$search}%");
                }
            });
        }

        // Apply date filters for revenue (per hari/per minggu/selected date/range)
        $filterLabel = null;
        $viewType = $request->get('view');
        if ($viewType === 'day') {
            $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
            $query->whereDate('date', $date->toDateString());
            $filterLabel = 'Per Hari: ' . $date->format('d/m/Y');
        } elseif ($viewType === 'week') {
            // Expect HTML week input like YYYY-Www
            $weekInput = $request->get('week');
            if ($weekInput && preg_match('/^(\d{4})-W(\d{2})$/', $weekInput, $m)) {
                $year = (int)$m[1];
                $week = (int)$m[2];
                $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
                $end = (clone $start)->endOfWeek();
            } else {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            }
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
            $filterLabel = 'Per Minggu: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        } elseif ($viewType === 'range') {
            $start = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
            $end = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;
            if ($start && $end) {
                $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
                $filterLabel = 'Rentang: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
            } elseif ($start) {
                $query->whereDate('date', '>=', $start->toDateString());
                $filterLabel = 'Mulai: ' . $start->format('d/m/Y');
            } elseif ($end) {
                $query->whereDate('date', '<=', $end->toDateString());
                $filterLabel = 'Sampai: ' . $end->format('d/m/Y');
            }
        }

    // Compute total pendapatan (sum of total_price) for all filtered results
    $totalPendapatan = (clone $query)->sum('total_price');

    // Calculate total profit from all filtered transactions
    $allTransactions = (clone $query)->with(['items.product'])->get();
    $totalProfit = 0;
    foreach($allTransactions as $transaction) {
        foreach($transaction->items as $item) {
            if($item->type === 'product' && $item->product) {
                $totalProfit += ($item->product->profit * $item->quantity);
            }
        }
    }

    $transactions = $query->latest()->paginate(10)->appends($request->query());

    // Also compute total for current page (optional)
    $pagePendapatan = $transactions->sum('total_price');

    // Overall margin for filtered results
    $totalMargin = $totalPendapatan > 0 ? ($totalProfit / $totalPendapatan * 100) : 0;

        return view('transactions.index', compact('transactions', 'totalPendapatan', 'totalProfit', 'totalMargin', 'pagePendapatan', 'filterLabel'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $lastTransaction = Transaction::orderBy('created_at', 'desc')->first();

        // Generate invoice number (MPyymmddxxxx)
        $invoiceNumber = 'MP' . date('ymd');
        if ($lastTransaction) {
            $lastNumber = substr($lastTransaction->invoice_number, -4);
            $newNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        $invoiceNumber .= $newNumber;

        return view('transactions.create', compact('customers', 'products', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'required|date',
            'invoice_number' => 'required|string',
            'items' => 'nullable|array',
            'types' => 'required|array|min:1',
            'quantities' => 'required|array|min:1',
            'discounts' => 'required|array|min:1',
            'service_names' => 'nullable|array',
            'service_prices' => 'nullable|array',
            'service_durations' => 'nullable|array',
            'service_types' => 'nullable|array',
            'service_imeis' => 'nullable|array',
            'service_statuses' => 'nullable|array',
            'service_product_ids' => 'nullable|array',
            'note' => 'nullable|string',
            'warranty_period' => 'nullable|integer|min:0',
            'discount' => 'required|numeric|min:0',
            'cash' => 'required|numeric|min:0',
            'tradein_old_phone' => 'nullable|string',
            'tradein_old_imei' => 'nullable|string',
            'tradein_old_value' => 'nullable|numeric|min:0',
            'tradein_new_product_id' => 'nullable|exists:products,id',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $items = [];

            // Calculate total amount and prepare items
            foreach ($request->types as $index => $type) {
                $quantity = $request->quantities[$index];
                $discount = $request->discounts[$index];

                if ($type === 'product') {
                    $itemId = $request->items[$index];
                    $product = Product::findOrFail($itemId);

                    $price = $product->sell_price;
                    $subtotal = ($price * $quantity) - $discount;

                    $items[] = [
                        'type' => 'product',
                        'product' => $product,
                        'service' => null,
                        'quantity' => $quantity,
                        'price' => $price,
                        'discount' => $discount,
                        'subtotal' => $subtotal,
                        'service_type' => null,
                        'imei' => null,
                        'service_name' => null,
                        'service_duration' => null,
                        'original_index' => $index
                    ];
                }

                $totalAmount += $subtotal;
            }

            // Apply global discount
            $totalAmount -= $request->discount; // includes trade-in credit if applied
            if ($totalAmount < 0) { $totalAmount = 0; }

            // Create payment record
            $payment = Payment::create([
                'method' => 'cash',
                'amount' => $totalAmount,
                'status' => 'paid'
            ]);

            // If no customer selected, use default "Umum" customer
            $customerId = $request->customer_id;
            if (!$customerId) {
                $defaultCustomer = Customer::where('email', 'umum@default.local')->first();
                $customerId = $defaultCustomer ? $defaultCustomer->id : null;
            }

            // Create transaction record
            // Determine transaction type based on trade-in presence
            $txType = $request->filled('tradein_new_product_id') ? 'trade-in' : 'purchase';

            // Calculate warranty expiration if warranty period is provided
            $warrantyExpiresAt = null;
            if ($request->warranty_period && $request->warranty_period > 0) {
                $warrantyExpiresAt = Carbon::parse($request->date)->addDays($request->warranty_period)->toDateString();
            }

            $transaction = Transaction::create([
                'customer_id' => $customerId,
                'type' => $txType,
                'invoice_number' => $request->invoice_number,
                'date' => $request->date,
                'total_price' => $totalAmount,
                'payment_id' => $payment->id,
                'notes' => $request->note,
                'warranty_period' => $request->warranty_period,
                'warranty_expires_at' => $warrantyExpiresAt,
                'cashier_id' => auth()->id()
            ]);

            // Create transaction items and update product stock
            foreach ($items as $item) {
                $transactionItem = [
                    'type' => $item['type'] === 'service_manual' ? 'service' : $item['type'],
                    'quantity' => $item['quantity'],
                    'price_per_item' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['subtotal']
                ];

                if ($item['type'] === 'product') {
                    $transactionItem['product_id'] = $item['product']->id;
                    // Update product stock
                    $item['product']->stock -= $item['quantity'];
                    $item['product']->save();
                } elseif ($item['type'] === 'service' && $item['service']) {
                    $transactionItem['service_id'] = $item['service']->id;
                } else {
                    // For manual service, service_id will be null
                    $transactionItem['service_id'] = null;
                }

                $transaction->items()->create($transactionItem);
            }

            // If trade-in payload exists, persist it and add old phone to products (stock in)
            if ($request->filled('tradein_new_product_id')) {
                $tradeIn = \App\Models\TradeIn::create([
                    'customer_id' => $customerId,
                    'old_phone' => $request->tradein_old_phone,
                    'old_imei' => $request->tradein_old_imei,
                    'old_value' => $request->tradein_old_value ?? 0,
                    'new_product_id' => $request->tradein_new_product_id,
                    'date' => $request->date,
                ]);

                // Create a product entry for the traded-in old phone and increase stock
                $newProduct = Product::find($request->tradein_new_product_id);
                $defaultCategoryId = Category::first()->id ?? Category::create(['name' => 'General', 'slug' => 'general'])->id;

                $oldPhoneName = $request->tradein_old_phone ?: 'Trade-in Item';
                $slugBase = Str::slug($oldPhoneName);
                $uniqueSlug = $slugBase . '-' . $transaction->id . '-' . now()->timestamp;

                Product::create([
                    'name' => $oldPhoneName,
                    'slug' => $uniqueSlug,
                    'category_id' => $defaultCategoryId,
                    'description' => 'Trade-in item from invoice ' . $transaction->invoice_number,
                    'buy_price' => (float) ($request->tradein_old_value ?? 0),
                    'sell_price' => (float) ($request->tradein_old_value ?? 0),
                    'gross_profit' => 0,
                    'net_profit' => 0,
                    'assessoris' => null,
                    'imei' => $request->tradein_old_imei,
                    'stock' => 1,
                ]);
            }

            DB::commit();

            \Log::info('Transaction created successfully', [
                'transaction_id' => $transaction->id,
                'invoice' => $transaction->invoice_number,
                'total' => $totalAmount
            ]);

            // After creating a transaction, go straight to its detail page
            return redirect()
                ->route('transaction.show', $transaction->id)
                ->with('success', 'Transaction created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Transaction validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['_token'])
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['_token'])
            ]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'items.product', 'payment']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $transaction->load(['customer', 'items.product', 'payment']);
        $customers = Customer::orderBy('name')->get();
        return view('transactions.edit', compact('transaction', 'customers'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:purchase,trade-in',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_item' => 'required|numeric|min:0',
            'delivery_cost' => 'nullable|numeric|min:0',
            'tax_cost' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit_card,debit,transfer',
            'payment_status' => 'required|in:paid,pending',
            'notes' => 'nullable|string',
            'warranty_period' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $transaction->load(['items.product', 'payment']);

            // Restore previous stock
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $product = $item->product;
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Update transaction
            // Calculate warranty expiration if warranty period is provided
            $warrantyExpiresAt = null;
            if ($request->warranty_period && $request->warranty_period > 0) {
                $warrantyExpiresAt = Carbon::parse($request->date)->addDays($request->warranty_period)->toDateString();
            }

            $transaction->update([
                'type' => $request->type,
                'delivery_cost' => $request->delivery_cost,
                'tax_cost' => $request->tax_cost,
                'total_price' => $request->total_price,
                'date' => $request->date,
                'notes' => $request->notes,
                'warranty_period' => $request->warranty_period,
                'warranty_expires_at' => $warrantyExpiresAt
            ]);

            // Update payment
            $transaction->payment->update([
                'method' => $request->payment_method,
                'amount' => $request->total_price,
                'status' => $request->payment_status
            ]);

            // Delete old items
            $transaction->items()->delete();

            // Create new items and update stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_per_item' => $item['price_per_item'],
                    'subtotal' => $item['quantity'] * $item['price_per_item']
                ]);

                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();
            return redirect()->route('transaction.show', $transaction)
                ->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            $transaction->load('items.product', 'payment');
            $invoiceNumber = $transaction->invoice_number;
            $paymentId = $transaction->payment_id;

            // Restore stock
            $restoredProducts = [];
            foreach ($transaction->items as $item) {
                if ($item->product) {
                    $product = $item->product;
                    $product->stock += $item->quantity;
                    $product->save();
                    $restoredProducts[] = $product->name . ' (+' . $item->quantity . ')';
                }
            }

            // Delete related records in correct order
            // 1. Delete transaction items first
            $transaction->items()->delete();

            // 2. Delete transaction (removes FK reference to payment)
            $transaction->delete();

            // 3. Delete payment last (after FK reference is removed)
            if ($paymentId) {
                Payment::where('id', $paymentId)->delete();
            }

            DB::commit();

            $message = "Transaksi {$invoiceNumber} berhasil dihapus.";
            if (!empty($restoredProducts)) {
                $message .= " Stok dikembalikan: " . implode(', ', $restoredProducts);
            }

            return redirect()->route('transaction.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['customer', 'items.product', 'payment', 'cashier']);
        return view('transactions.print', compact('transaction'));
    }

    public function invoice(Transaction $transaction)
    {
        $transaction->load(['customer', 'items.product', 'payment', 'cashier']);
        return view('transactions.invoice', compact('transaction'));
    }
}
