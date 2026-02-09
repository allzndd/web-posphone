<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosToko;
use App\Models\PosProduk;
use App\Models\PosProdukMerk;
use App\Models\PosProdukStok;
use App\Models\PosPelanggan;
use App\Models\PosTukarTambah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports index page
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        return view('reports.index');
    }

    /**
     * Display the financial report
     */
    public function financial(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        // Get query parameters
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $storeId = $request->get('store_id', '');
        $merkId = $request->get('merk_id', '');

        // Determine date range
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } elseif ($period === 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($period === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period === 'all') {
            $start = Carbon::createFromYear(2000);
            $end = Carbon::now()->endOfDay();
        } else { // default month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Format dates for the view
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        // Get all stores and merks for filters
        $stores = PosToko::where('owner_id', $ownerId)->get();
        $merks = PosProdukMerk::where('owner_id', $ownerId)->orWhere('is_global', 1)->get();

        // Build base query for sales transactions
        $salesQuery = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1);

        // Apply filters
        if ($storeId) {
            $salesQuery->where('pos_toko_id', $storeId);
        }

        // Get sales transactions with items
        $salesTransactions = $salesQuery->with(['items.produk', 'toko'])->get();

        // Calculate core metrics
        $totalSalesCount = $salesTransactions->count();
        $totalRevenue = 0;
        $totalHPP = 0;
        $itemDetails = [];

        foreach ($salesTransactions as $transaction) {
            foreach ($transaction->items as $item) {
                // Skip if merk filter is applied and doesn't match
                if ($merkId && $item->produk && $item->produk->pos_produk_merk_id != $merkId) {
                    continue;
                }

                $quantity = $item->quantity ?? 0;
                $hargaJual = $item->harga_satuan ?? ($item->produk ? $item->produk->harga_jual : 0);
                $hargaBeli = $item->produk ? $item->produk->harga_beli : 0;

                $itemRevenue = $hargaJual * $quantity;
                $itemHpp = $hargaBeli * $quantity;

                $totalRevenue += $itemRevenue;
                $totalHPP += $itemHpp;

                $productType = $item->produk && $item->produk->product_type ? $item->produk->product_type : 'electronic';
                $grossProfit = $itemRevenue - $itemHpp;
                $grossMargin = $itemRevenue > 0 ? ($grossProfit / $itemRevenue) * 100 : 0;

                $itemDetails[] = [
                    'invoice' => $transaction->invoice,
                    'product_name' => $item->produk ? $item->produk->nama : 'Unknown',
                    'product_type' => $productType,
                    'quantity' => $quantity,
                    'revenue' => $itemRevenue,
                    'hpp' => $itemHpp,
                    'gross_profit' => $grossProfit,
                    'gross_margin' => $grossMargin,
                ];
            }
        }

        // Calculate profit metrics
        $grossProfit = $totalRevenue - $totalHPP;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Get operating expenses (transactions marked as expenses: is_transaksi_masuk = 0 and pos_kategori_expense_id is set)
        $expenseQuery = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->with(['kategoriExpense', 'toko']);

        if ($storeId) {
            $expenseQuery->where('pos_toko_id', $storeId);
        }

        $expenses = $expenseQuery->orderBy('created_at', 'desc')->get();
        $totalOperatingExpenses = $expenses->sum('total_harga');

        $netProfit = $grossProfit - $totalOperatingExpenses;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Cash flow data
        $cashIn = $totalRevenue;
        $cashOut = $totalHPP + $totalOperatingExpenses;
        $freeCashFlow = $cashIn - $cashOut;

        // Get receivables (unpaid transactions)
        $receivableQuery = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'paid')
                  ->orWhereNull('payment_status');
            });

        if ($storeId) {
            $receivableQuery->where('pos_toko_id', $storeId);
        }

        $receivable = $receivableQuery->sum(DB::raw('total_harga - COALESCE(paid_amount, 0)'));

        // Payment method breakdown
        $paymentBreakdown = [];
        if ($salesTransactions->count() > 0) {
            $paymentData = DB::table('pos_transaksi')
                ->where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 1)
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('pos_toko_id', $storeId);
                })
                ->select('metode_pembayaran', DB::raw('SUM(total_harga) as total'))
                ->groupBy('metode_pembayaran')
                ->get();

            foreach ($paymentData as $data) {
                $paymentBreakdown[strtolower($data->metode_pembayaran ?? 'other')] = $data->total;
            }
        }

        // Cash balance per outlet
        $cashBalancePerOutlet = [];
        $outletData = DB::table('pos_transaksi')
            ->where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->select(
                'pos_toko_id',
                DB::raw('SUM(CASE WHEN is_transaksi_masuk = 1 THEN total_harga ELSE 0 END) as cash_in'),
                DB::raw('SUM(CASE WHEN is_transaksi_masuk = 0 THEN total_harga ELSE 0 END) as cash_out')
            )
            ->groupBy('pos_toko_id')
            ->get();

        foreach ($stores as $store) {
            $outlet = $outletData->where('pos_toko_id', $store->id)->first();
            $cashInValue = $outlet ? $outlet->cash_in : 0;
            $cashOutValue = $outlet ? $outlet->cash_out : 0;

            $cashBalancePerOutlet[] = [
                'store_name' => $store->nama,
                'cash_in' => $cashInValue,
                'cash_out' => $cashOutValue,
                'balance' => $cashInValue - $cashOutValue,
            ];
        }

        // Expenses by type (from kategoriExpense)
        $expensesByType = [];
        foreach ($expenses->groupBy('kategoriExpense.nama') as $type => $expenseGroup) {
            if ($type && $type !== 'null') {
                $expensesByType[$type] = $expenseGroup->sum('total_harga');
            }
        }

        return view('reports.financial', [
            'totalRevenue' => $totalRevenue,
            'totalSalesCount' => max(1, $totalSalesCount), // At least show 1 if there are any items
            'totalHPP' => $totalHPP,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'totalOperatingExpenses' => $totalOperatingExpenses,
            'netProfit' => $netProfit,
            'netMargin' => $netMargin,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'freeCashFlow' => $freeCashFlow,
            'receivable' => $receivable,
            'paymentBreakdown' => $paymentBreakdown,
            'cashBalancePerOutlet' => $cashBalancePerOutlet,
            'itemDetails' => $itemDetails,
            'expensesByType' => $expensesByType,
            'expenses' => $expenses,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'storeId' => $storeId,
            'merkId' => $merkId,
            'stores' => $stores,
            'merks' => $merks,
        ]);
    }

    /**
     * Export financial report to Excel
     */
    public function exportFinancial(Request $request)
    {
        // Similar implementation to get financial data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }

    /**
     * Display the sales report
     */
    public function sales(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        // Get query parameters
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $storeId = $request->get('store_id', '');

        // Determine date range
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } elseif ($period === 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($period === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period === 'all') {
            $start = Carbon::createFromYear(2000);
            $end = Carbon::now()->endOfDay();
        } else { // default month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Format dates for the view
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        // Get all stores for this owner
        $stores = PosToko::where('owner_id', $ownerId)->get();

        // Build base query for sales transactions
        $transactionsQuery = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->with(['toko', 'pelanggan']);

        // Apply store filter
        if ($storeId) {
            $transactionsQuery->where('pos_toko_id', $storeId);
        }

        // Get all transactions
        $transactions = $transactionsQuery->orderBy('created_at', 'desc')->get();

        // Calculate metrics
        $totalTransactions = $transactions->count();
        $totalSales = $transactions->sum('total_harga');
        $totalItems = 0;

        // Get total items from transaction items
        $transactionIds = $transactions->pluck('id')->toArray();
        if (!empty($transactionIds)) {
            $totalItems = PosTransaksiItem::whereIn('pos_transaksi_id', $transactionIds)->sum('quantity');
        }

        $averageTransaction = $totalTransactions > 0 ? round($totalSales / $totalTransactions, 0) : 0;

        return view('reports.sales', [
            'transactions' => $transactions,
            'totalTransactions' => $totalTransactions,
            'totalSales' => $totalSales,
            'totalItems' => $totalItems,
            'averageTransaction' => $averageTransaction,
            'stores' => $stores,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'storeId' => $storeId,
        ]);
    }

    /**
     * Export sales report to Excel
     */
    public function exportSales(Request $request)
    {
        // Similar implementation to get sales data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }


    /**
     * Display the customers report
     */
    public function customers(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        $sortBy = $request->get('sort_by', 'name');

        // Get all customers for this owner with their transactions
        $customers = \App\Models\PosPelanggan::where('owner_id', $ownerId)
            ->with(['transaksi' => function ($query) {
                $query->where('is_transaksi_masuk', 1); // Only count sales
            }])
            ->get();

        // Calculate metrics for each customer
        $customersData = $customers->map(function ($customer) {
            $purchaseCount = $customer->transaksi->count();
            $purchaseValue = $customer->transaksi->sum('total_harga');

            return [
                'customer' => $customer,
                'purchaseCount' => $purchaseCount,
                'purchaseValue' => $purchaseValue,
            ];
        });

        // Sort customers based on sort_by parameter
        if ($sortBy === 'purchases') {
            $customersData = $customersData->sortByDesc('purchaseCount');
        } elseif ($sortBy === 'value') {
            $customersData = $customersData->sortByDesc('purchaseValue');
        } else { // 'name' or default
            $customersData = $customersData->sortBy(function ($item) {
                return $item['customer']->nama;
            });
        }

        // Reorder to maintain the original customer objects for the view
        $customers = $customersData->pluck('customer')->values();

        // Calculate summary metrics
        $totalCustomers = $customersData->count();
        $totalPurchases = $customersData->sum('purchaseCount');
        $totalValue = $customersData->sum('purchaseValue');
        $averageValue = $totalCustomers > 0 ? round($totalValue / $totalCustomers, 2) : 0;

        return view('reports.customers', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'totalPurchases' => $totalPurchases,
            'totalValue' => $totalValue,
            'averageValue' => $averageValue,
            'sortBy' => $sortBy,
        ]);
    }

    /**
     * Export customers report to Excel
     */
    public function exportCustomers(Request $request)
    {
        // Similar implementation to get customers data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }

    /**
     * Display the stock report
     */
    public function stock(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        $storeId = $request->get('store_id', '');
        $lowStockOnly = $request->has('low_stock') && $request->get('low_stock') == 1;

        // Get all stores for this owner
        $stores = PosToko::where('owner_id', $ownerId)->get();

        // Build base query for stock records
        $stockQuery = PosProdukStok::where('owner_id', $ownerId)
            ->with(['produk', 'toko']);

        // Apply store filter
        if ($storeId) {
            $stockQuery->where('pos_toko_id', $storeId);
        }

        // Apply low stock filter
        if ($lowStockOnly) {
            $stockQuery->where('stok', '<=', 5);
        }

        // Get all stock records
        $stocks = $stockQuery->get();

        // Calculate metrics
        $totalItems = $stocks->count();
        $totalStock = $stocks->sum('stok');
        $lowStockItems = $stocks->where('stok', '<=', 5)->where('stok', '>', 0)->count();
        $outOfStock = $stocks->where('stok', 0)->count();

        return view('reports.stock', [
            'stocks' => $stocks,
            'totalItems' => $totalItems,
            'totalStock' => $totalStock,
            'lowStockItems' => $lowStockItems,
            'outOfStock' => $outOfStock,
            'stores' => $stores,
            'storeId' => $storeId,
            'lowStockOnly' => $lowStockOnly,
        ]);
    }

    /**
     * Export stock report to Excel
     */
    public function exportStock(Request $request)
    {
        // Similar implementation to get stock data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }

    /**
     * Display the products report
     */
    public function products(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        $categoryFilter = $request->get('category', '');
        $brandFilter = $request->get('brand', '');

        // Get all brands for this owner
        $brands = PosProdukMerk::where('owner_id', $ownerId)
            ->orWhere('is_global', 1)
            ->get();

        // Get unique categories from products
        $categories = collect();
        if ($ownerId) {
            $categories = DB::table('pos_produk')
                ->select('product_type')
                ->where('owner_id', $ownerId)
                ->distinct()
                ->get();
        }

        // Build base query for products
        $productsQuery = PosProduk::where('owner_id', $ownerId)
            ->with(['merk', 'stok']);

        // Apply brand filter
        if ($brandFilter) {
            $productsQuery->where('pos_produk_merk_id', $brandFilter);
        }

        // Apply category filter (using product_type)
        if ($categoryFilter) {
            $productsQuery->where('product_type', $categoryFilter);
        }

        // Get all products
        $products = $productsQuery->get();

        // Calculate metrics
        $totalProducts = $products->count();
        $totalStock = 0;
        $totalValue = 0;

        foreach ($products as $product) {
            $productStock = $product->stok->sum('stok');
            $productValue = $productStock * ($product->harga_jual ?? 0);
            $totalStock += $productStock;
            $totalValue += $productValue;
        }

        return view('reports.products', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'categories' => $categories,
            'brands' => $brands,
            'categoryFilter' => $categoryFilter,
            'brandFilter' => $brandFilter,
        ]);
    }

    /**
     * Export products report to Excel
     */
    public function exportProducts(Request $request)
    {
        // Similar implementation to get products data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }

    /**
     * Display the trade-in report
     */
    public function tradeIn(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner_id ?? ($user->owner ? $user->owner->id : null);

        if (!$ownerId) {
            return redirect()->route('login')->with('error', 'Owner tidak ditemukan');
        }

        // Get query parameters
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $storeId = $request->get('store_id', '');

        // Determine date range
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } elseif ($period === 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($period === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period === 'all') {
            $start = Carbon::createFromYear(2000);
            $end = Carbon::now()->endOfDay();
        } else { // default month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Format dates for the view
        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        // Get all stores for this owner
        $stores = PosToko::where('owner_id', $ownerId)->get();

        // Build base query for trade-ins
        $tradeInsQuery = PosTukarTambah::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->with(['pelanggan', 'produkMasuk', 'produkKeluar', 'toko']);

        // Apply store filter
        if ($storeId) {
            $tradeInsQuery->where('pos_toko_id', $storeId);
        }

        // Get all trade-in records
        $tradeIns = $tradeInsQuery->orderBy('created_at', 'desc')->get();

        // Calculate metrics
        $totalTradeIns = $tradeIns->count();
        $totalTradeInValue = 0;
        $totalAdditionalPayment = 0;
        $totalValue = 0;

        foreach ($tradeIns as $tradeIn) {
            // Get transaction for this trade-in (usually 2 transactions: one for old product, one for new)
            $transactions = $tradeIn->transaksi;
            
            foreach ($transactions as $transaksi) {
                if ($transaksi->is_transaksi_masuk == 0) {
                    // Incoming product (old item being traded in)
                    $totalTradeInValue += $transaksi->total_harga;
                } elseif ($transaksi->is_transaksi_masuk == 1) {
                    // Outgoing product (new item being sold)
                    $totalAdditionalPayment += $transaksi->total_harga;
                }
            }
        }

        $totalValue = $totalTradeInValue + $totalAdditionalPayment;

        return view('reports.trade-in', [
            'tradeIns' => $tradeIns,
            'totalTradeIns' => $totalTradeIns,
            'totalTradeInValue' => $totalTradeInValue,
            'totalAdditionalPayment' => $totalAdditionalPayment,
            'totalValue' => $totalValue,
            'stores' => $stores,
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'storeId' => $storeId,
        ]);
    }

    /**
     * Export trade-in report to Excel
     */
    public function exportTradeIn(Request $request)
    {
        // Similar implementation to get trade-in data and export
        // For now, returning a placeholder
        return response()->json(['message' => 'Export functionality pending']);
    }
}
