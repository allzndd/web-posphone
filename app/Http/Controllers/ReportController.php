<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PosTransaksi;
use App\Models\PosTukarTambah;
use App\Models\PosProduk;
use App\Models\ProdukStok;
use App\Models\PosPelanggan;
use App\Models\PosToko;
use App\Models\PosProdukMerk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $storeId = $request->get('store_id');

        $query = PosTransaksi::with(['toko', 'items.produk', 'pelanggan'])
            ->where('is_transaksi_masuk', 1); // Transaksi masuk (penjualan)

        // Apply date filters
        if ($period == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply store filter
        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        // Calculate summary
        $totalSales = $transactions->sum('total_harga');
        $totalTransactions = $transactions->count();
        $totalItems = $transactions->sum(function($t) {
            return $t->items->sum('quantity');
        });
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Get stores for filter
        $stores = PosToko::where('owner_id', auth()->user()->owner_id)->get();

        return view('reports.sales', compact(
            'transactions', 
            'totalSales', 
            'totalTransactions', 
            'totalItems',
            'averageTransaction',
            'period',
            'startDate',
            'endDate',
            'stores',
            'storeId'
        ));
    }

    public function tradeIn(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = PosTukarTambah::with(['pelanggan', 'produkMasuk', 'produkKeluar', 'toko', 'transaksiPenjualan', 'transaksiPembelian']);

        // Apply date filters based on created_at
        if ($period == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $tradeIns = $query->orderBy('created_at', 'desc')->get();

        // Calculate summary
        $totalTradeIns = $tradeIns->count();
        $totalTradeInValue = $tradeIns->sum(function($tt) {
            return $tt->transaksiPembelian ? $tt->transaksiPembelian->total_harga : 0;
        });
        $totalAdditionalPayment = $tradeIns->sum(function($tt) {
            return $tt->transaksiPenjualan ? $tt->transaksiPenjualan->total_harga : 0;
        });
        $totalValue = $totalTradeInValue + $totalAdditionalPayment;

        return view('reports.trade-in', compact(
            'tradeIns',
            'totalTradeIns',
            'totalTradeInValue',
            'totalAdditionalPayment',
            'totalValue',
            'period',
            'startDate',
            'endDate'
        ));
    }

    public function products(Request $request)
    {
        $categoryFilter = $request->get('category');
        $brandFilter = $request->get('brand');

        $query = PosProduk::with(['merk', 'stok']);

        if ($brandFilter) {
            $query->where('pos_produk_merk_id', $brandFilter);
        }

        $products = $query->orderBy('nama')->get();

        // Calculate summary
        $totalProducts = $products->count();
        $totalStock = $products->sum(function($p) {
            return $p->stok->sum('stok');
        });
        $totalValue = $products->sum(function($p) {
            return $p->stok->sum(function($s) use ($p) {
                return $s->stok * $p->harga_jual;
            });
        });

        // Get brands for filters
        $brands = PosProdukMerk::all();
        $categories = collect(); // Empty collection since there's no kategori in this model

        return view('reports.products', compact(
            'products',
            'totalProducts',
            'totalStock',
            'totalValue',
            'categories',
            'brands',
            'categoryFilter',
            'brandFilter'
        ));
    }

    public function stock(Request $request)
    {
        $storeId = $request->get('store_id');
        $lowStockOnly = $request->get('low_stock', false);

        $query = ProdukStok::with(['produk', 'toko']);

        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        } else {
            // Filter by owner's stores
            $query->whereHas('toko', function($q) {
                $q->where('owner_id', auth()->user()->owner_id);
            });
        }

        if ($lowStockOnly) {
            // Assuming low stock means stok <= 5 (you can adjust this)
            $query->where('stok', '<=', 5);
        }

        $stocks = $query->orderBy('stok', 'asc')->get();

        // Calculate summary
        $totalItems = $stocks->count();
        $totalStock = $stocks->sum('stok');
        $lowStockItems = $stocks->where('stok', '<=', 5)->count(); // Adjust threshold as needed
        $outOfStock = $stocks->where('stok', 0)->count();

        // Get stores for filter
        $stores = PosToko::where('owner_id', auth()->user()->owner_id)->get();

        return view('reports.stock', compact(
            'stocks',
            'totalItems',
            'totalStock',
            'lowStockItems',
            'outOfStock',
            'stores',
            'storeId',
            'lowStockOnly'
        ));
    }

    public function customers(Request $request)
    {
        $sortBy = $request->get('sort_by', 'name');

        $query = PosPelanggan::with(['transaksi']);

        if ($sortBy == 'purchases') {
            $query->withCount('transaksi')->orderBy('transaksi_count', 'desc');
        } elseif ($sortBy == 'value') {
            $query->withSum('transaksi', 'total_harga')->orderBy('transaksi_sum_total_harga', 'desc');
        } else {
            $query->orderBy('nama');
        }

        $customers = $query->get();

        // Calculate summary
        $totalCustomers = $customers->count();
        $totalPurchases = $customers->sum(function($c) {
            return $c->transaksi->count();
        });
        $totalValue = $customers->sum(function($c) {
            return $c->transaksi->sum('total_harga');
        });
        $averageValue = $totalCustomers > 0 ? $totalValue / $totalCustomers : 0;

        return view('reports.customers', compact(
            'customers',
            'totalCustomers',
            'totalPurchases',
            'totalValue',
            'averageValue',
            'sortBy'
        ));
    }

    public function financial(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Determine date range
        if ($period == 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period == 'month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($period == 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Sales (Incoming transactions - is_transaksi_masuk = 1)
        $salesTransactions = PosTransaksi::where('is_transaksi_masuk', 1)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalSales = $salesTransactions->sum('total_harga');
        $totalSalesCount = $salesTransactions->count();

        // Purchases (Outgoing transactions - is_transaksi_masuk = 0)
        $purchaseTransactions = PosTransaksi::where('is_transaksi_masuk', 0)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalPurchases = $purchaseTransactions->sum('total_harga');
        $totalPurchasesCount = $purchaseTransactions->count();

        // Trade-ins
        $tradeIns = PosTukarTambah::whereBetween('created_at', [$start, $end])->get();
        $totalTradeIns = $tradeIns->sum(function($tt) {
            return $tt->transaksiPembelian ? $tt->transaksiPembelian->total_harga : 0;
        });

        // Calculate profit/loss
        $grossProfit = $totalSales - $totalPurchases;
        $netProfit = $grossProfit; // You can add expenses here

        return view('reports.financial', compact(
            'totalSales',
            'totalSalesCount',
            'totalPurchases',
            'totalPurchasesCount',
            'totalTradeIns',
            'grossProfit',
            'netProfit',
            'period',
            'startDate',
            'endDate'
        ));
    }
}
