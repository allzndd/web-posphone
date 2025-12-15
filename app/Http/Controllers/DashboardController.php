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

        // TODO: Update dengan tabel baru pos_transaksi, pos_pelanggan, pos_produk
        // Statistics - Temporary disabled until migration complete
        $totalTransactions = 0;
        $totalCustomers = 0;
        $totalProducts = 0;
        $totalProfit = 0;

        // Revenue calculations - Temporary disabled
        $todayRevenue = 0;
        $thisWeekRevenue = 0;
        $thisMonthRevenue = 0;
        $thisYearRevenue = 0;

        // Profit calculations - Temporary disabled
        $todayProfit = 0;
        $thisWeekProfit = 0;
        $thisMonthProfit = 0;
        $thisYearProfit = 0;
        $lastMonthProfit = 0;
        $monthlyGrowth = 0;

        // Chart data - Temporary disabled
        $chartLabels = [];
        $revenueData = [];
        $profitData = [];

        // Recent transactions - Temporary disabled
        $recentTransactions = collect([]);

        // Top selling products - Temporary disabled
        $topProductsData = [];

        // Trade-in statistics - Temporary disabled
        $totalTradeIns = 0;
        $tradeInValue = 0;

        // Low stock products - Temporary disabled
        $lowStockProducts = collect([]);

        // Recommendations - Temporary disabled
        $topProfitProducts = collect([]);
        $bestSellingProducts = collect([]);
        $popularCustomers = collect([]);

        return view('pages.dashboard', compact(
            'totalTransactions',
            'totalCustomers',
            'totalProducts',
            'totalProfit',
            'todayRevenue',
            'thisWeekRevenue',
            'thisMonthRevenue',
            'thisYearRevenue',
            'todayProfit',
            'thisWeekProfit',
            'thisMonthProfit',
            'thisYearProfit',
            'monthlyGrowth',
            'chartLabels',
            'revenueData',
            'profitData',
            'recentTransactions',
            'topProductsData',
            'totalTradeIns',
            'tradeInValue',
            'lowStockProducts',
            'topProfitProducts',
            'bestSellingProducts',
            'popularCustomers',
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

        // Get transactions data
        $transaksiMasuk = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.produk', 'toko', 'pelanggan'])
            ->get();

        $transaksiKeluar = \App\Models\PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.produk', 'toko', 'supplier'])
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
            fputcsv($file, ['FINANCIAL REPORT']);
            fputcsv($file, ['Period:', $startDate . ' to ' . $endDate]);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            // Summary
            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Total Income', number_format($totalIncome, 0, ',', '.')]);
            fputcsv($file, ['Total Expense', number_format($totalExpense, 0, ',', '.')]);
            fputcsv($file, ['Net Profit', number_format($netProfit, 0, ',', '.')]);
            fputcsv($file, []);
            
            // Income Transactions
            fputcsv($file, ['INCOME TRANSACTIONS (SALES)']);
            fputcsv($file, ['Date', 'Invoice', 'Store', 'Customer', 'Payment Method', 'Status', 'Total']);
            
            foreach ($transaksiMasuk as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('Y-m-d H:i'),
                    $trans->invoice,
                    $trans->toko->nama ?? '-',
                    $trans->pelanggan->nama ?? '-',
                    $trans->metode_pembayaran,
                    $trans->status,
                    number_format($trans->total_harga, 0, ',', '.')
                ]);
            }
            
            fputcsv($file, []);
            
            // Expense Transactions
            fputcsv($file, ['EXPENSE TRANSACTIONS (PURCHASES)']);
            fputcsv($file, ['Date', 'Invoice', 'Store', 'Supplier', 'Payment Method', 'Status', 'Total']);
            
            foreach ($transaksiKeluar as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('Y-m-d H:i'),
                    $trans->invoice,
                    $trans->toko->nama ?? '-',
                    $trans->supplier->nama ?? '-',
                    $trans->metode_pembayaran,
                    $trans->status,
                    number_format($trans->total_harga, 0, ',', '.')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
