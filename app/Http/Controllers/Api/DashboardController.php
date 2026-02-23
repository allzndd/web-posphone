<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosProduk;
use App\Models\PosPelanggan;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            \Log::info('Dashboard Stats Request', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'owner_id' => $ownerId,
                'has_owner' => $user->owner !== null
            ]);
            
            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found for this user',
                ], 403);
            }

            // Total Transactions (semua transaksi, baik masuk maupun keluar)
            $totalTransactions = PosTransaksi::where('owner_id', $ownerId)->count();
            
            \Log::info('Total Transactions', ['count' => $totalTransactions]);

            // Calculate profit (Income - Expense) - Only COMPLETED transactions
            // is_transaksi_masuk = 1 artinya transaksi masuk (penjualan/income)
            // is_transaksi_masuk = 0 artinya transaksi keluar (pembelian/expense)
            $totalIncome = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->sum('total_harga');
                
            $totalExpense = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->sum('total_harga');
                
            $totalProfit = $totalIncome - $totalExpense;
            
            \Log::info('Profit Calculation', [
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'profit' => $totalProfit
            ]);
            
            // Period Profits - Only COMPLETED transactions
            $todayProfit = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_harga')
                - PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_harga');
                
            $thisWeekProfit = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('total_harga')
                - PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('total_harga');
                
            $thisMonthProfit = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_harga')
                - PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_harga');
                
            $thisYearProfit = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('total_harga')
                - PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->where('status', 'completed')
                ->whereYear('created_at', now()->year)
                ->sum('total_harga');

            // Total Products (jumlah stok dari ProdukStok)
            $totalProducts = ProdukStok::where('owner_id', $ownerId)->sum('stok');
            
            \Log::info('Total Products', ['count' => $totalProducts]);

            // Total Customers
            $totalCustomers = PosPelanggan::where('owner_id', $ownerId)->count();
            
            \Log::info('Total Customers', ['count' => $totalCustomers]);

            $result = [
                'total_profit' => round($totalProfit, 2),
                'total_transactions' => $totalTransactions,
                'total_products' => $totalProducts,
                'total_customers' => $totalCustomers,
                'today_profit' => round($todayProfit, 2),
                'week_profit' => round($thisWeekProfit, 2),
                'month_profit' => round($thisMonthProfit, 2),
                'year_profit' => round($thisYearProfit, 2),
            ];
            
            \Log::info('Dashboard Stats Result', $result);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Stats Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sales chart data
     */
    public function getSalesChart(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            if (!$ownerId) {
                return response()->json(['success' => false, 'message' => 'Owner not found'], 403);
            }
            $period = $request->input('period', 'week'); // week, month, year

            $startDate = Carbon::now();
            $groupBy = 'DATE(created_at)';
            $dateFormat = '%Y-%m-%d';

            switch ($period) {
                case 'month':
                    $startDate = $startDate->subMonth();
                    break;
                case 'year':
                    $startDate = $startDate->subYear();
                    $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                    $dateFormat = '%Y-%m';
                    break;
                default: // week
                    $startDate = $startDate->subWeek();
                    break;
            }

            $chartData = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1) // Penjualan (income)
                ->where('status', 'completed') // Only completed transactions
                ->where('created_at', '>=', $startDate)
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '$dateFormat') as date"),
                    DB::raw('SUM(total_harga) as value')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'value' => (float) $item->value,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sales chart',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get top selling products
     */
    public function getTopProducts(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            if (!$ownerId) {
                return response()->json(['success' => false, 'message' => 'Owner not found'], 403);
            }
            $limit = $request->input('limit', 5);

            $topProducts = PosTransaksiItem::whereHas('transaksi', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1) // Penjualan (income)
                    ->where('status', 'completed'); // Only completed transactions
            })
                ->select('pos_produk_id', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('pos_produk_id')
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    $product = PosProduk::find($item->pos_produk_id);
                    if ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->nama ?? $product->display_name ?? 'Unknown',
                            'price' => (float) $product->harga_jual,
                            'sold' => (int) $item->total_sold,
                        ];
                    }
                    return null;
                })
                ->filter()
                ->values();

            return response()->json([
                'success' => true,
                'data' => $topProducts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get top products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get low stock products
     */
    public function getLowStock(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            if (!$ownerId) {
                return response()->json(['success' => false, 'message' => 'Owner not found'], 403);
            }
            
            $threshold = $request->input('threshold', 5);

            // Menggunakan ProdukStok seperti di web dashboard
            $lowStockProducts = ProdukStok::where('owner_id', $ownerId)
                ->where('stok', '<=', $threshold)
                ->with('produk')
                ->get()
                ->filter(function($stok) {
                    // Filter out orphaned stock records (where product has been deleted)
                    return $stok->produk !== null;
                })
                ->map(function($stok) {
                    return [
                        'id' => $stok->produk->id,
                        'name' => $stok->produk->nama,
                        'stock' => (int) $stok->stok,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'data' => $lowStockProducts,
            ]);
        } catch (\Exception $e) {
            \Log::error('Low Stock Error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get low stock products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            if (!$ownerId) {
                return response()->json(['success' => false, 'message' => 'Owner not found'], 403);
            }
            
            $limit = $request->input('limit', 10);

            // Menggunakan logika yang sama dengan web dashboard
            $recentTransactions = PosTransaksi::where('owner_id', $ownerId)
                ->with(['toko', 'pelanggan', 'supplier'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($trans) {
                    return [
                        'id' => $trans->id,
                        'invoice_number' => $trans->invoice,
                        'customer' => [
                            'name' => $trans->is_transaksi_masuk 
                                ? ($trans->pelanggan->nama ?? '-') 
                                : ($trans->supplier->nama ?? '-')
                        ],
                        'total_price' => (float) $trans->total_harga,
                        'date' => $trans->created_at->format('Y-m-d H:i:s'),
                        'payment' => [
                            'status' => $trans->status ?? 'Selesai'
                        ],
                        'transaction_type' => $trans->is_transaksi_masuk ? 'income' : 'expense',
                        'metode_pembayaran' => $trans->metode_pembayaran ?? 'Cash',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recentTransactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
