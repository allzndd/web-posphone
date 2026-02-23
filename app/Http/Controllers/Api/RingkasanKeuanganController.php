<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use App\Models\PosToko;
use App\Models\PosExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FinancialReportExport;

class RingkasanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        try {
            \Log::info('🔍 Financial index called');
            $user = $request->user();
            \Log::info('🔍 User: ' . ($user ? $user->id : 'null'));
            
            $ownerId = $user->owner ? $user->owner->id : null;
            \Log::info('🔍 Owner ID: ' . ($ownerId ?? 'null'));

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else { // default month
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $type = $request->get('type'); // 'revenue', 'expense', or null for all

            $query = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->with(['pelanggan', 'supplier']);

            // Search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('invoice', 'LIKE', "%{$search}%")
                      ->orWhereHas('pelanggan', function($q) use ($search) {
                          $q->where('nama', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('nama', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Filter by type
            if ($type === 'revenue') {
                $query->where('is_transaksi_masuk', 1);
            } elseif ($type === 'expense') {
                $query->where('is_transaksi_masuk', 0);
            }

            $transactions = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Calculate totals for current filter (only completed transactions)
            $totalRevenue = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->sum('total_harga');
            
            $totalExpenses = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->sum('total_harga');
            
            $netProfit = $totalRevenue - $totalExpenses;

            // Transform data
            $transformedData = $transactions->getCollection()->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'code' => $transaction->invoice ?? '-',
                    'type' => $transaction->is_transaksi_masuk ? 'revenue' : 'expense',
                    'type_label' => $transaction->is_transaksi_masuk ? 'Pemasukan' : 'Pengeluaran',
                    'amount' => $transaction->total_harga,
                    'customer_name' => $transaction->pelanggan->nama ?? '-',
                    'supplier_name' => $transaction->supplier->nama ?? '-',
                    'partner_name' => $transaction->is_transaksi_masuk 
                        ? ($transaction->pelanggan->nama ?? '-') 
                        : ($transaction->supplier->nama ?? '-'),
                    'date' => $transaction->created_at->format('Y-m-d'),
                    'time' => $transaction->created_at->format('H:i:s'),
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

            // Get cash flow data
            $cashFlowData = $this->getCashFlowData($ownerId, $start, $end);
            
            // Calculate financial metrics using data from cash flow
            $revenue = $cashFlowData['cash_in'];
            $hpp = $cashFlowData['hpp'];
            $grossProfit = $revenue - $hpp;
            $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
            $operatingExpenses = $cashFlowData['operating_expenses'];
            $netProfit = $grossProfit - $operatingExpenses;
            $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
            
            $transactionCount = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan keuangan berhasil diambil',
                'summary' => [
                    'period_start' => $start->toDateString(),
                    'period_end' => $end->toDateString(),
                    'revenue' => $revenue,
                    'hpp' => $hpp,
                    'gross_profit' => $grossProfit,
                    'gross_margin' => round($grossMargin, 2),
                    'operating_expenses' => $operatingExpenses,
                    'net_profit' => $netProfit,
                    'net_margin' => round($netMargin, 2),
                    'transaction_count' => $transactionCount,
                ],
                'cash_flow' => $cashFlowData,
                'payment_methods' => $this->getPaymentMethodsData($ownerId, $start, $end),
                'balance_per_outlet' => $this->getBalancePerOutlet($ownerId, $start, $end),
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'last_page' => $transactions->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in RingkasanKeuanganController index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan keuangan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSummary(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            // Get cash flow data for consistency
            $cashFlowData = $this->getCashFlowData($ownerId, $start, $end);
            
            // Calculate financial metrics using data from cash flow
            $revenue = $cashFlowData['cash_in'];
            $hpp = $cashFlowData['hpp'];
            $grossProfit = $revenue - $hpp;
            $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
            $operatingExpenses = $cashFlowData['operating_expenses'];
            $netProfit = $grossProfit - $operatingExpenses;
            $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

            $transactionCount = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->count();

            \Log::info('📊 Financial Summary Data:', [
                'revenue' => $revenue,
                'hpp' => $hpp,
                'gross_profit' => $grossProfit,
                'gross_margin' => $grossMargin,
                'operating_expenses' => $operatingExpenses,
                'net_profit' => $netProfit,
                'net_margin' => $netMargin,
                'transaction_count' => $transactionCount,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'revenue' => $revenue,
                    'hpp' => $hpp,
                    'gross_profit' => $grossProfit,
                    'gross_margin' => round($grossMargin, 2),
                    'operating_expenses' => $operatingExpenses,
                    'net_profit' => $netProfit,
                    'net_margin' => round($netMargin, 2),
                    'transaction_count' => $transactionCount,
                    'period_start' => $start->toDateString(),
                    'period_end' => $end->toDateString(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            // Get cash flow data
            $cashFlowData = $this->getCashFlowData($ownerId, $start, $end);
            
            // Calculate financial metrics using data from cash flow
            $revenue = $cashFlowData['cash_in'];
            $hpp = $cashFlowData['hpp'];
            $grossProfit = $revenue - $hpp;
            $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
            $operatingExpenses = $cashFlowData['operating_expenses'];
            $netProfit = $grossProfit - $operatingExpenses;
            $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
            
            $transactionCount = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->count();

            // Get transactions for detail sheet and calculate revenue/hpp manually like web (only completed)
            $transactions = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'completed')
                ->with(['pelanggan', 'supplier', 'toko', 'items.produk'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate revenue and HPP from transaction items (like web) - only from sales
            $revenue = 0;
            $hpp = 0;
            $detailPerItem = [];

            foreach ($transactions as $transaction) {
                // Only calculate revenue/hpp from sales transactions
                if ($transaction->is_transaksi_masuk == 1) {
                    foreach ($transaction->items as $item) {
                        $produk = $item->produk;
                        $quantity = $item->quantity;
                        $hargaJual = $item->harga_satuan ?? ($produk ? $produk->harga_jual : 0);
                        $hargaBeli = $produk ? $produk->harga_beli : 0;

                        $itemRevenue = $hargaJual * $quantity;
                        $itemHpp = $hargaBeli * $quantity;

                        $revenue += $itemRevenue;
                        $hpp += $itemHpp;

                        $productType = $produk && $produk->product_type ? $produk->product_type : 'unknown';
                        $typeBadge = [
                            'electronic' => 'Electronic',
                            'accessory' => 'Accessory',
                            'service' => 'Service',
                            'unknown' => 'Unknown'
                        ][$productType];

                        $detailPerItem[] = [
                            'nama_produk' => $produk ? $produk->nama : 'Unknown',
                            'type_badge' => $typeBadge,
                            'quantity' => $quantity,
                            'harga_jual' => $hargaJual,
                            'harga_beli' => $hargaBeli,
                            'revenue' => $itemRevenue,
                            'hpp' => $itemHpp,
                            'gross_profit' => $itemRevenue - $itemHpp
                        ];
                    }
                }
            }

            // Get expenses for expenses detail sheet
            $expenses = PosExpense::where('owner_id', $ownerId)
                ->whereBetween('expense_date', [$start, $end])
                ->with('toko')
                ->orderBy('expense_date', 'desc')
                ->get();

            $operatingExpenses = $expenses->sum('amount');

            // Calculate financial metrics (like web)
            $grossProfit = $revenue - $hpp;
            $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
            $netProfit = $grossProfit - $operatingExpenses;
            $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

            // Cash Flow (like web)
            $cashIn = $revenue;
            $cashOut = $hpp + $operatingExpenses;
            $freeCashFlow = $cashIn - $cashOut;

            // Payment Methods (like web)
            $paymentMethods = $transactions->groupBy('metode_pembayaran')->map(function($group) {
                return $group->sum('total_harga');
            })->toArray();

            // Receivable (like web)
            $receivable = $transactions->where('payment_status', '!=', 'paid')->sum(function($t) {
                return $t->total_harga - ($t->paid_amount ?? 0);
            });

            // Prepare export data
            $exportData = [
                'period_start' => $start->format('d-m-Y'),
                'period_end' => $end->format('d-m-Y'),
                'revenue' => $revenue,
                'hpp' => $hpp,
                'grossProfit' => $grossProfit,
                'grossMargin' => round($grossMargin, 2),
                'operatingExpenses' => $operatingExpenses,
                'netProfit' => $netProfit,
                'netMargin' => round($netMargin, 2),
                'transactionCount' => $transactions->count(),
                'cashIn' => $cashIn,
                'cashOut' => $cashOut,
                'freeCashFlow' => $freeCashFlow,
                'receivable' => $receivable,
                'paymentMethods' => $paymentMethods,
                'transactions' => $transactions,
                'expenses' => $expenses,
                'detailPerItem' => $detailPerItem,
                'recentExpenses' => $expenses, // Same as expenses for Excel
            ];

            return Excel::download(
                new FinancialReportExport($exportData),
                'Laporan_Keuangan_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error in exportExcel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Cash Flow Data (Cash In, Cash Out, Free Cash Flow, Piutang)
     */
    private function getCashFlowData($ownerId, $start, $end)
    {
        // Cash In = Total revenue from sales transactions (only completed)
        $cashIn = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->sum('total_harga');

        // Calculate HPP from product cost (harga_beli) per item sold (only completed)
        $salesTransactions = PosTransaksi::with('items.produk')
            ->where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->get();

        $totalHPP = 0;
        foreach ($salesTransactions as $transaction) {
            foreach ($transaction->items as $item) {
                $quantity = $item->quantity ?? 1;
                $hargaBeli = $item->produk->harga_beli ?? 0;
                $totalHPP += $hargaBeli * $quantity;
            }
        }

        // Cash Out = Purchases + Operating Expenses (only completed)
        $totalPurchases = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->sum('total_harga');

        // Get operating expenses if PosExpense model exists
        $operatingExpenses = 0;
        if (class_exists('App\Models\PosExpense')) {
            $operatingExpenses = \App\Models\PosExpense::where('owner_id', $ownerId)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');
        }

        $cashOut = $totalPurchases + $operatingExpenses;

        // Free Cash Flow = Cash In - Cash Out
        $freeCashFlow = $cashIn - $cashOut;

        // Piutang (Accounts Receivable) - completed transactions with payment_status != 'paid'
        $piutang = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->where('payment_status', '!=', 'paid')
            ->sum(\DB::raw('total_harga - COALESCE(paid_amount, 0)'));

        return [
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'free_cash_flow' => $freeCashFlow,
            'piutang' => $piutang,
            'hpp' => $totalHPP, // Add HPP to cash flow data
            'operating_expenses' => $operatingExpenses,
        ];
    }

    /**
     * Get Payment Methods Breakdown
     */
    private function getPaymentMethodsData($ownerId, $start, $end)
    {
        $transactions = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->select('metode_pembayaran', \DB::raw('SUM(total_harga) as total'))
            ->groupBy('metode_pembayaran')
            ->get();

        $grandTotal = $transactions->sum('total');

        $result = [];
        foreach ($transactions as $transaction) {
            $percentage = $grandTotal > 0 ? ($transaction->total / $grandTotal) * 100 : 0;
            $result[] = [
                'method' => $transaction->metode_pembayaran ?? 'Unknown',
                'total' => $transaction->total,
                'percentage' => round($percentage, 2),
            ];
        }

        return $result;
    }

    /**
     * Get Cash Balance Per Outlet (Toko)
     */
    private function getBalancePerOutlet($ownerId, $start, $end)
    {
        // Get all stores for this owner
        $allStores = PosToko::where('owner_id', $ownerId)->get();

        // Get transaction data grouped by store (only completed)
        $transactionData = PosTransaksi::where('owner_id', $ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->select('pos_toko_id', 
                \DB::raw('SUM(CASE WHEN is_transaksi_masuk = 1 THEN total_harga ELSE 0 END) as cash_in'),
                \DB::raw('SUM(CASE WHEN is_transaksi_masuk = 0 THEN total_harga ELSE 0 END) as cash_out')
            )
            ->groupBy('pos_toko_id')
            ->get()
            ->keyBy('pos_toko_id');

        $result = [];
        foreach ($allStores as $store) {
            $transaction = $transactionData->get($store->id);
            $cashIn = $transaction ? $transaction->cash_in : 0;
            $cashOut = $transaction ? $transaction->cash_out : 0;
            $balance = $cashIn - $cashOut;

            $result[] = [
                'toko_id' => $store->id,
                'toko_name' => $store->nama,
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'balance' => $balance,
            ];
        }

        return $result;
    }

    /**
     * Get Detail Per Item (Transaction Items Detail)
     */
    public function getDetailPerItem(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $search = $request->get('search', '');
            $storeId = $request->get('store_id');
            $productName = $request->get('product_name');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            // Get transaction items with details (only completed transactions)
            $items = \DB::table('pos_transaksi_item')
                ->join('pos_transaksi', 'pos_transaksi_item.pos_transaksi_id', '=', 'pos_transaksi.id')
                ->leftJoin('pos_produk', 'pos_transaksi_item.pos_produk_id', '=', 'pos_produk.id')
                ->where('pos_transaksi.owner_id', $ownerId)
                ->whereBetween('pos_transaksi.created_at', [$start, $end])
                ->where('pos_transaksi.is_transaksi_masuk', 1) // Only revenue transactions (sales)
                ->where('pos_transaksi.status', 'completed') // Only completed transactions
                ->when($storeId, function ($query) use ($storeId) {
                    return $query->where('pos_transaksi.pos_toko_id', $storeId);
                })
                ->when($productName, function ($query) use ($productName) {
                    return $query->where('pos_produk.nama', $productName);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('pos_transaksi.invoice', 'LIKE', "%{$search}%")
                          ->orWhere('pos_produk.nama', 'LIKE', "%{$search}%");
                    });
                })
                ->select(
                    'pos_transaksi.invoice',
                    'pos_produk.nama as product_name',
                    'pos_produk.product_type',
                    'pos_transaksi_item.quantity as qty',
                    'pos_transaksi_item.harga_satuan as price',
                    'pos_produk.harga_beli as cost_price',
                    \DB::raw('(pos_transaksi_item.quantity * pos_transaksi_item.harga_satuan) as revenue'),
                    \DB::raw('(pos_transaksi_item.quantity * COALESCE(pos_produk.harga_beli, 0)) as total_hpp'),
                    \DB::raw('((pos_transaksi_item.quantity * pos_transaksi_item.harga_satuan) - (pos_transaksi_item.quantity * COALESCE(pos_produk.harga_beli, 0))) as gross_profit')
                )
                ->orderBy('pos_transaksi.created_at', 'desc')
                ->paginate(20);

            // Calculate margin percentage for each item
            $transformedItems = $items->map(function ($item) {
                $margin = $item->revenue > 0 ? (($item->gross_profit / $item->revenue) * 100) : 0;
                return [
                    'invoice' => $item->invoice,
                    'product_name' => $item->product_name ?? 'Unknown',
                    'type' => ucfirst($item->product_type ?? 'Electronic'),
                    'qty' => $item->qty,
                    'revenue' => $item->revenue,
                    'hpp' => $item->total_hpp,
                    'gross_profit' => $item->gross_profit,
                    'margin' => round($margin, 2),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedItems,
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'last_page' => $items->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in getDetailPerItem: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail per item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Operating Expenses Detail
     */
    public function getOperatingExpenses(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            // Get expenses from PosExpense table (like web)
            $expenses = PosExpense::where('owner_id', $ownerId)
                ->whereBetween('expense_date', [$start, $end])
                ->with('toko')
                ->orderBy('expense_date', 'desc')
                ->limit(10)
                ->get();

            // Breakdown by expense_type
            $expensesByType = $expenses->groupBy('expense_type');
            $totalExpenses = $expenses->sum('amount');
            
            $breakdown = [];
            foreach ($expensesByType as $type => $items) {
                $amount = $items->sum('amount');
                $percentage = $totalExpenses > 0 ? ($amount / $totalExpenses) * 100 : 0;
                $breakdown[] = [
                    'category' => ucfirst($type),
                    'amount' => $amount,
                    'percentage' => round($percentage, 2),
                ];
            }

            $recentExpenses = $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'receipt_number' => $expense->receipt_number ?? 'N/A',
                    'date' => $expense->expense_date->format('Y-m-d'),
                    'expense_type' => ucfirst($expense->expense_type),
                    'toko' => $expense->toko ? $expense->toko->nama : 'N/A',
                    'description' => $expense->description ?? 'N/A',
                    'amount' => $expense->amount,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'breakdown' => $breakdown,
                    'recent_expenses' => $recentExpenses,
                    'total_expenses' => $totalExpenses,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in getOperatingExpenses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil operating expenses: ' . $e->getMessage(),
            ], 500);
        }
    }
}
