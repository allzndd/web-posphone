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
}
