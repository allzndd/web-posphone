<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Redirect superadmin to their own dashboard
        if(auth()->user()->isSuperadmin()) {
            return redirect()->route('dashboard-superadmin');
        }

        // Get filter period (default: week)
        $period = $request->get('period', 'week');
        
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Statistics
        $totalTransactions = \App\Models\PosTransaksi::where('owner_id', $ownerId)->count();
        $totalCustomers = \App\Models\PosPelanggan::where('owner_id', $ownerId)->count();
        $totalProducts = \App\Models\ProdukStok::where('owner_id', $ownerId)->sum('stok');
        
        // Calculate profit (Income - Expense) - Only COMPLETED transactions
        $totalIncome = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->sum('total_harga');
        $totalExpense = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->sum('total_harga');
        $totalProfit = $totalIncome - $totalExpense;

        // Profit calculations - Only COMPLETED transactions
        $todayProfit = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_harga')
            - \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_harga');
            
        $thisWeekProfit = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_harga')
            - \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_harga');
            
        $thisMonthProfit = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga')
            - \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');
            
        $thisYearProfit = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->sum('total_harga')
            - \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        // Chart data based on period
        $chartLabels = [];
        $profitData = [];
        
        if ($period === 'yearly') {
            // Last 5 years - Only COMPLETED transactions
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $chartLabels[] = $year;
                
                $income = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1)
                    ->where('status', 'completed')
                    ->whereYear('created_at', $year)
                    ->sum('total_harga');
                $expense = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 0)
                    ->where('status', 'completed')
                    ->whereYear('created_at', $year)
                    ->sum('total_harga');
                    
                $profitData[] = $income - $expense;
            }
        } elseif ($period === 'monthly') {
            // Last 12 months - Only COMPLETED transactions
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $chartLabels[] = $date->format('M Y');
                
                $income = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1)
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total_harga');
                $expense = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 0)
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total_harga');
                    
                $profitData[] = $income - $expense;
            }
        } else {
            // Last 7 days (week) - Only COMPLETED transactions
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('D, M j');
                
                $income = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('total_harga');
                $expense = \App\Models\PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 0)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $date)
                    ->sum('total_harga');
                    
                $profitData[] = $income - $expense;
            }
        }

        // Recent transactions (limited to 10)
        $recentTransactions = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->with(['toko', 'pelanggan', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($trans) {
                return (object)[
                    'id' => $trans->id,
                    'invoice_number' => $trans->invoice,
                    'customer' => (object)['name' => $trans->is_transaksi_masuk ? ($trans->pelanggan->nama ?? '-') : ($trans->supplier->nama ?? '-')],
                    'total_price' => $trans->total_harga,
                    'date' => $trans->created_at,
                    'payment' => (object)['status' => $trans->status],
                    'transaction_type' => $trans->is_transaksi_masuk ? 'income' : 'expense'
                ];
            });

        // Trade-in statistics
        $totalTradeIns = \App\Models\PosTukarTambah::where('owner_id', $ownerId)->count();
        $tradeInValue = 0; // TODO: Calculate from pos_tukar_tambah if needed

        // Low stock products
        $lowStockProducts = \App\Models\ProdukStok::where('owner_id', $ownerId)
            ->where('stok', '<=', 5)
            ->with('produk')
            ->get()
            ->filter(function($stok) {
                // Filter out orphaned stock records (where product has been deleted)
                return $stok->produk !== null;
            })
            ->map(function($stok) {
                return (object)[
                    'id' => $stok->produk->id,
                    'name' => $stok->produk->nama,
                    'stock' => $stok->stok
                ];
            });

        // Top products data (for sales chart) - Using transaction items for accurate tracking
        $topProductsData = \App\Models\PosTransaksiItem::whereHas('transaksi', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1) // Only sales
                    ->where('status', 'completed'); // Only completed
            })
            ->with('produk')
            ->get()
            ->filter(function($item) {
                return $item->produk !== null;
            })
            ->groupBy(function($item) {
                return $item->produk->nama ?? 'Unknown';
            })
            ->map(function($items, $nama) {
                $totalSold = $items->sum('quantity');
                $totalRevenue = $items->sum(function($item) {
                    return $item->harga_satuan * $item->quantity;
                });
                
                return [
                    'name' => $nama,
                    'sold' => $totalSold,
                    'revenue' => $totalRevenue
                ];
            })
            ->sortByDesc('sold')
            ->take(5)
            ->values()
            ->toArray();

        // Recommendations - Top Profit Products (from actual completed transactions)
        $topProfitProducts = \App\Models\PosTransaksiItem::whereHas('transaksi', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1) // Only sales transactions
                    ->where('status', 'completed'); // Only completed
            })
            ->with(['produk', 'transaksi'])
            ->get()
            ->filter(function($item) {
                // Filter out items with deleted products or invalid prices
                return $item->produk !== null 
                    && $item->harga_satuan > 0 
                    && $item->produk->harga_beli > 0;
            })
            ->groupBy(function($item) {
                // Group by product name
                return $item->produk->nama ?? 'Unknown';
            })
            ->map(function($items, $nama) {
                // Calculate total profit from actual sales
                $totalProfit = $items->sum(function($item) {
                    // Profit per unit = harga jual (harga_satuan) - harga beli (dari master produk)
                    $unitProfit = ($item->harga_satuan ?? 0) - ($item->produk->harga_beli ?? 0);
                    return $unitProfit * $item->quantity;
                });
                
                // Count total sold units
                $totalSold = $items->sum('quantity');
                
                // Get current stock (sum from all variants with same name)
                $productIds = $items->pluck('pos_produk_id')->unique();
                $totalStok = \App\Models\ProdukStok::whereIn('pos_produk_id', $productIds)
                    ->where('owner_id', $items->first()->transaksi->owner_id)
                    ->sum('stok');
                
                return (object)[
                    'name' => $nama,
                    'profit' => $totalProfit,
                    'stock' => $totalStok,
                    'sold' => $totalSold
                ];
            })
            ->sortByDesc('profit')
            ->take(5)
            ->values();

        // Best Selling Products (from actual completed transactions)
        $bestSellingProducts = \App\Models\PosTransaksiItem::whereHas('transaksi', function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1) // Only sales transactions
                    ->where('status', 'completed'); // Only completed
            })
            ->with(['produk', 'transaksi'])
            ->get()
            ->filter(function($item) {
                // Filter out items with deleted products
                return $item->produk !== null;
            })
            ->groupBy(function($item) {
                // Group by product name
                return $item->produk->nama ?? 'Unknown';
            })
            ->map(function($items, $nama) {
                // Count total sold units
                $totalSold = $items->sum('quantity');
                
                // Get average sell price (from harga_satuan in transaction items)
                $avgSellPrice = $items->avg('harga_satuan');
                
                return (object)[
                    'name' => $nama,
                    'sell_price' => $avgSellPrice,
                    'total_sold' => $totalSold
                ];
            })
            ->sortByDesc('total_sold')
            ->take(5)
            ->values();

        // Popular Customers (highest spending)
        $popularCustomers = \App\Models\PosPelanggan::where('owner_id', $ownerId)
            ->withCount(['transaksi' => function($query) {
                $query->where('is_transaksi_masuk', 1);
            }])
            ->withSum(['transaksi' => function($query) {
                $query->where('is_transaksi_masuk', 1);
            }], 'total_harga')
            ->having('transaksi_count', '>', 0)
            ->orderByDesc('transaksi_sum_total_harga')
            ->limit(5)
            ->get()
            ->map(function($customer) {
                return (object)[
                    'name' => $customer->nama,
                    'phone' => $customer->nomor_hp ?? '-',
                    'total_spent' => $customer->transaksi_sum_total_harga ?? 0,
                    'total_transactions' => $customer->transaksi_count
                ];
            });

        // Current Balance per Outlet (All Time - Saldo Kas Riil)
        $stores = \App\Models\PosToko::where('owner_id', $ownerId)->get();
        $currentBalancePerOutlet = [];
        $currentBalanceData = DB::table('pos_transaksi')
            ->where('owner_id', $ownerId)
            ->where('status', 'completed')
            ->select(
                'pos_toko_id',
                DB::raw('SUM(CASE WHEN is_transaksi_masuk = 1 THEN total_harga ELSE 0 END) as total_cash_in'),
                DB::raw('SUM(CASE WHEN is_transaksi_masuk = 0 THEN total_harga ELSE 0 END) as total_cash_out')
            )
            ->groupBy('pos_toko_id')
            ->get();

        $totalCurrentBalance = 0;
        foreach ($stores as $store) {
            // Get store modal (initial capital)
            $modal = $store->modal ?? 0;
            
            // Get all time cash in and cash out
            $storeData = $currentBalanceData->where('pos_toko_id', $store->id)->first();
            $totalCashIn = $storeData ? $storeData->total_cash_in : 0;
            $totalCashOut = $storeData ? $storeData->total_cash_out : 0;
            
            // Calculate current balance: Modal + Cash In - Cash Out
            $currentBalance = $modal + $totalCashIn - $totalCashOut;
            $totalCurrentBalance += $currentBalance;
            
            $currentBalancePerOutlet[] = (object)[
                'store_name' => $store->nama,
                'modal' => $modal,
                'total_cash_in' => $totalCashIn,
                'total_cash_out' => $totalCashOut,
                'current_balance' => $currentBalance,
            ];
        }

        return view('pages.dashboard', compact(
            'totalTransactions',
            'totalCustomers',
            'totalProducts',
            'totalProfit',
            'todayProfit',
            'thisWeekProfit',
            'thisMonthProfit',
            'thisYearProfit',
            'chartLabels',
            'profitData',
            'recentTransactions',
            'topProductsData',
            'totalTradeIns',
            'tradeInValue',
            'lowStockProducts',
            'topProfitProducts',
            'bestSellingProducts',
            'popularCustomers',
            'currentBalancePerOutlet',
            'totalCurrentBalance',
            'period'
        ));
    }

    public function downloadFinancialReport(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get date range
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Add time to dates for proper filtering
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Get transactions data
        $transaksiMasuk = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->with(['toko', 'pelanggan'])
            ->orderBy('created_at', 'asc')
            ->get();

        $transaksiKeluar = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->with(['toko', 'supplier'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate totals
        $totalIncome = $transaksiMasuk->sum('total_harga');
        $totalExpense = $transaksiKeluar->sum('total_harga');
        $netProfit = $totalIncome - $totalExpense;

        // Generate CSV
        $filename = 'financial_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transaksiMasuk, $transaksiKeluar, $startDate, $endDate, $totalIncome, $totalExpense, $netProfit) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Report Header
            fputcsv($file, ['FINANCIAL REPORT', '', '', '', '', '', '']);
            fputcsv($file, ['Period', $startDate . ' to ' . $endDate, '', '', '', '', '']);
            fputcsv($file, ['Generated', now()->format('d/m/Y H:i:s'), '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '', '']);
            
            // Summary
            fputcsv($file, ['SUMMARY', '', '', '', '', '', '']);
            fputcsv($file, ['Total Income', 'Rp ' . number_format($totalIncome, 0, ',', '.'), '', '', '', '', '']);
            fputcsv($file, ['Total Expense', 'Rp ' . number_format($totalExpense, 0, ',', '.'), '', '', '', '', '']);
            fputcsv($file, ['Net Profit', 'Rp ' . number_format($netProfit, 0, ',', '.'), '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '', '']);
            
            // Income Transactions
            fputcsv($file, ['INCOME TRANSACTIONS (SALES)', '', '', '', '', '', '']);
            fputcsv($file, ['Date', 'Invoice', 'Store', 'Customer', 'Payment Method', 'Status', 'Total']);
            
            foreach ($transaksiMasuk as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('d/m/Y H:i'),
                    $trans->invoice,
                    $trans->toko->nama ?? '-',
                    $trans->pelanggan->nama ?? '-',
                    ucfirst($trans->metode_pembayaran),
                    ucfirst($trans->status),
                    'Rp ' . number_format($trans->total_harga, 0, ',', '.')
                ]);
            }
            
            fputcsv($file, ['', '', '', '', '', '', '']);
            
            // Expense Transactions
            fputcsv($file, ['EXPENSE TRANSACTIONS (PURCHASES)', '', '', '', '', '', '']);
            fputcsv($file, ['Date', 'Invoice', 'Store', 'Supplier', 'Payment Method', 'Status', 'Total']);
            
            foreach ($transaksiKeluar as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('d/m/Y H:i'),
                    $trans->invoice,
                    $trans->toko->nama ?? '-',
                    $trans->supplier->nama ?? '-',
                    ucfirst($trans->metode_pembayaran),
                    ucfirst($trans->status),
                    'Rp ' . number_format($trans->total_harga, 0, ',', '.')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
