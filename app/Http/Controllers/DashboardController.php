<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Service;
use App\Models\TradeIn;
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

        // Statistics
        $totalTransactions = Transaction::count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::sum('stock');

        // Calculate total profit from all transactions
        $allTransactions = Transaction::with(['items.product'])->get();
        $totalProfit = 0;
        foreach($allTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $totalProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        // Revenue calculations
        $todayRevenue = Transaction::whereDate('date', Carbon::today())->sum('total_price');
        $thisWeekRevenue = Transaction::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('total_price');
        $thisMonthRevenue = Transaction::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('total_price');
        $thisYearRevenue = Transaction::whereYear('date', Carbon::now()->year)->sum('total_price');

        // Profit calculations for periods
        $todayTransactions = Transaction::with(['items.product'])->whereDate('date', Carbon::today())->get();
        $todayProfit = 0;
        foreach($todayTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $todayProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        $thisWeekTransactions = Transaction::with(['items.product'])->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get();
        $thisWeekProfit = 0;
        foreach($thisWeekTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $thisWeekProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        $thisMonthTransactions = Transaction::with(['items.product'])
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->get();
        $thisMonthProfit = 0;
        foreach($thisMonthTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $thisMonthProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        $thisYearTransactions = Transaction::with(['items.product'])
            ->whereYear('date', Carbon::now()->year)
            ->get();
        $thisYearProfit = 0;
        foreach($thisYearTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $thisYearProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        // Last month profit for comparison
        $lastMonthTransactions = Transaction::with(['items.product'])
            ->whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereYear('date', Carbon::now()->subMonth()->year)
            ->get();
        $lastMonthProfit = 0;
        foreach($lastMonthTransactions as $transaction) {
            foreach($transaction->items as $item) {
                if($item->type === 'product' && $item->product) {
                    $lastMonthProfit += ($item->product->profit * $item->quantity);
                }
            }
        }

        // Calculate growth percentage based on profit
        $monthlyGrowth = $lastMonthProfit > 0
            ? round((($thisMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100, 1)
            : 0;

        // Chart data based on period
        $chartLabels = [];
        $revenueData = [];
        $profitData = [];

        if ($period === 'monthly') {
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $chartLabels[] = $date->format('M Y');
                $revenueData[] = Transaction::whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->sum('total_price');

                // Calculate profit for this month
                $monthTransactions = Transaction::with(['items.product'])
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->get();
                $monthProfit = 0;
                foreach($monthTransactions as $transaction) {
                    foreach($transaction->items as $item) {
                        if($item->type === 'product' && $item->product) {
                            $monthProfit += ($item->product->profit * $item->quantity);
                        }
                    }
                }
                $profitData[] = $monthProfit;
            }
        } elseif ($period === 'yearly') {
            // Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i)->year;
                $chartLabels[] = $year;
                $revenueData[] = Transaction::whereYear('date', $year)->sum('total_price');

                // Calculate profit for this year
                $yearTransactions = Transaction::with(['items.product'])
                    ->whereYear('date', $year)
                    ->get();
                $yearProfit = 0;
                foreach($yearTransactions as $transaction) {
                    foreach($transaction->items as $item) {
                        if($item->type === 'product' && $item->product) {
                            $yearProfit += ($item->product->profit * $item->quantity);
                        }
                    }
                }
                $profitData[] = $yearProfit;
            }
        } else {
            // Last 7 days (default)
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartLabels[] = $date->format('D, M j');
                $revenueData[] = Transaction::whereDate('date', $date)->sum('total_price');

                // Calculate profit for this day
                $dayTransactions = Transaction::with(['items.product'])
                    ->whereDate('date', $date)
                    ->get();
                $dayProfit = 0;
                foreach($dayTransactions as $transaction) {
                    foreach($transaction->items as $item) {
                        if($item->type === 'product' && $item->product) {
                            $dayProfit += ($item->product->profit * $item->quantity);
                        }
                    }
                }
                $profitData[] = $dayProfit;
            }
        }

        // Recent transactions
        $recentTransactions = Transaction::with(['customer', 'items.product'])
            ->latest()
            ->limit(5)
            ->get();

        // Top selling products
        // Gabungkan produk dengan nama sama (case-insensitive)
        $topProductsRaw = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->select(DB::raw('LOWER(products.name) as name_lower'), 'products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'), DB::raw('SUM(transaction_items.subtotal) as total_revenue'))
            ->where('transaction_items.type', 'product')
            ->whereNotNull('transaction_items.product_id')
            ->groupBy('name_lower', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $topProductsData = [];
        foreach ($topProductsRaw as $item) {
            $topProductsData[] = [
                'name' => $item->name,
                'sold' => $item->total_sold,
                'revenue' => $item->total_revenue
            ];
        }

        // Trade-in statistics
        $totalTradeIns = TradeIn::count();
        $tradeInValue = TradeIn::sum('old_value');

        // Low stock products
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->limit(5)->get();

        // Recommendations: Top Profit Products (group by name)
        // Use net_profit column from database
        $topProfitProductsRaw = Product::where('stock', '>', 0)
            ->select(
                DB::raw('LOWER(name) as name_lower'),
                'name',
                DB::raw('SUM(stock) as total_stock'),
                DB::raw('AVG(net_profit) as avg_profit')
            )
            ->groupBy('name_lower', 'name')
            ->orderByDesc('avg_profit')
            ->limit(5)
            ->get();

        $topProfitProducts = $topProfitProductsRaw->map(function($item) {
            return (object)[
                'name' => $item->name,
                'stock' => $item->total_stock,
                'profit' => round($item->avg_profit)
            ];
        });

        // Recommendations: Best Selling Products (group by name)
        $bestSellingProducts = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->select(DB::raw('LOWER(products.name) as name_lower'), 'products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'), DB::raw('AVG(products.sell_price) as avg_price'))
            ->where('transaction_items.type', 'product')
            ->whereNotNull('transaction_items.product_id')
            ->groupBy('name_lower', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'name' => $item->name,
                    'total_sold' => $item->total_sold,
                    'sell_price' => round($item->avg_price)
                ];
            });

        // Popular Customers (based on total transaction value)
        $popularCustomers = DB::table('transactions')
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->select(
                'customers.id',
                'customers.name',
                'customers.phone',
                DB::raw('COUNT(transactions.id) as total_transactions'),
                DB::raw('SUM(transactions.total_price) as total_spent')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

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
