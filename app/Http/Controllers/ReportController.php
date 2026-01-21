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
use App\Models\PosExpense;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersReportExport;
use App\Exports\FinancialReportExport;
use App\Exports\ProductsReportExport;
use App\Exports\SalesReportExport;
use App\Exports\StockReportExport;
use App\Exports\TradeInReportExport;

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

        // Get owner_id from logged in user
        $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
        $ownerId = $owner ? $owner->id : 0;
        
        // Get stores for filter
        $stores = PosToko::where('owner_id', $ownerId)->get();

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
        $storeId = $request->get('store_id');

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

        // Apply store filter
        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
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

        // Get owner_id from logged in user
        $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
        $ownerId = $owner ? $owner->id : 0;
        
        // Get stores for filter
        $stores = PosToko::where('owner_id', $ownerId)->get();

        return view('reports.trade-in', compact(
            'tradeIns',
            'totalTradeIns',
            'totalTradeInValue',
            'totalAdditionalPayment',
            'totalValue',
            'period',
            'startDate',
            'endDate',
            'stores',
            'storeId'
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
            // Get owner_id from logged in user
            $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
            $ownerId = $owner ? $owner->id : 0;
            
            // Filter by owner's stores
            $query->whereHas('toko', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
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

        // Get owner_id from logged in user
        $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
        $ownerId = $owner ? $owner->id : 0;
        
        // Get stores for filter
        $stores = PosToko::where('owner_id', $ownerId)->get();

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
        $storeId = $request->get('store_id');
        $merkId = $request->get('merk_id');

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

        // ==================== SALES DATA (Revenue) ====================
        $salesQuery = PosTransaksi::with(['items.produk.merk', 'toko'])
            ->where('is_transaksi_masuk', 1)
            ->whereBetween('created_at', [$start, $end]);
        
        if ($storeId) {
            $salesQuery->where('pos_toko_id', $storeId);
        }
        
        if ($merkId) {
            $salesQuery->whereHas('items.produk', function($q) use ($merkId) {
                $q->where('pos_produk_merk_id', $merkId);
            });
        }
        
        $salesTransactions = $salesQuery->get();
        $totalRevenue = $salesTransactions->sum('total_harga');
        $totalSalesCount = $salesTransactions->count();

        // Calculate HPP (COGS) per item
        $itemDetails = [];
        $totalHPP = 0;
        
        foreach ($salesTransactions as $transaction) {
            foreach ($transaction->items as $item) {
                $produk = $item->produk;
                $quantity = $item->quantity ?? 1;
                $revenue = $item->harga_satuan * $quantity;
                $hpp = ($produk->harga_beli ?? 0) * $quantity;
                $grossProfit = $revenue - $hpp;
                $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

                $totalHPP += $hpp;

                $itemDetails[] = [
                    'transaction_id' => $transaction->id,
                    'invoice' => $transaction->invoice,
                    'product_name' => $produk->nama ?? 'Unknown',
                    'product_type' => $produk->product_type ?? 'electronic',
                    'quantity' => $quantity,
                    'revenue' => $revenue,
                    'hpp' => $hpp,
                    'gross_profit' => $grossProfit,
                    'gross_margin' => $grossMargin,
                    'date' => $transaction->created_at,
                ];
            }
        }

        // ==================== CORE METRICS ====================
        $grossProfit = $totalRevenue - $totalHPP;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Operating Expenses
        $expensesQuery = PosExpense::whereBetween('expense_date', [$start, $end]);
        if ($storeId) {
            $expensesQuery->where('pos_toko_id', $storeId);
        }
        $expenses = $expensesQuery->get();
        $totalOperatingExpenses = $expenses->sum('amount');
        
        // Breakdown by type
        $expensesByType = $expenses->groupBy('expense_type')->map(function($items) {
            return $items->sum('amount');
        });

        // Net Profit & Margin
        $netProfit = $grossProfit - $totalOperatingExpenses;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // ==================== CASH FLOW ====================
        // Cash IN
        $cashIn = $totalRevenue;

        // Cash OUT
        $purchasesQuery = PosTransaksi::where('is_transaksi_masuk', 0)
            ->whereBetween('created_at', [$start, $end]);
        if ($storeId) {
            $purchasesQuery->where('pos_toko_id', $storeId);
        }
        $totalPurchases = $purchasesQuery->sum('total_harga');
        $cashOut = $totalPurchases + $totalOperatingExpenses;

        // Free Cash Flow
        $freeCashFlow = $cashIn - $cashOut;

        // Payment Method Breakdown
        $paymentMethodQuery = PosTransaksi::where('is_transaksi_masuk', 1)
            ->whereBetween('created_at', [$start, $end]);
        if ($storeId) {
            $paymentMethodQuery->where('pos_toko_id', $storeId);
        }
        
        $paymentBreakdown = $paymentMethodQuery
            ->select('metode_pembayaran', DB::raw('SUM(total_harga) as total'))
            ->groupBy('metode_pembayaran')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->metode_pembayaran => $item->total];
            });

        // Get owner_id from logged in user
        $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
        $ownerId = $owner ? $owner->id : 0;
        
        // Get stores for filter
        $stores = PosToko::where('owner_id', $ownerId)->get();
        
        // Get product names/merks for filter
        $merks = \App\Models\PosProdukMerk::where('owner_id', $ownerId)->orderBy('nama')->get();
        
        // Cash Balance per Outlet
        $cashBalancePerOutlet = [];
        
        // Filter stores if specific store is selected
        $storesToDisplay = $storeId ? $stores->where('id', $storeId) : $stores;
        
        foreach ($storesToDisplay as $store) {
            $storeRevenue = PosTransaksi::where('is_transaksi_masuk', 1)
                ->where('pos_toko_id', $store->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');
            
            $storePurchases = PosTransaksi::where('is_transaksi_masuk', 0)
                ->where('pos_toko_id', $store->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');
            
            $storeExpenses = PosExpense::where('pos_toko_id', $store->id)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');
            
            $cashBalancePerOutlet[] = [
                'store_name' => $store->nama,
                'cash_in' => $storeRevenue,
                'cash_out' => $storePurchases + $storeExpenses,
                'balance' => $storeRevenue - ($storePurchases + $storeExpenses),
            ];
        }

        // Receivable & Payable
        $receivableQuery = PosTransaksi::where('is_transaksi_masuk', 1)
            ->where('payment_status', '!=', 'paid')
            ->whereBetween('created_at', [$start, $end]);
        if ($storeId) {
            $receivableQuery->where('pos_toko_id', $storeId);
        }
        $receivable = $receivableQuery->sum(DB::raw('total_harga - paid_amount'));

        $payableQuery = PosTransaksi::where('is_transaksi_masuk', 0)
            ->where('payment_status', '!=', 'paid')
            ->whereBetween('created_at', [$start, $end]);
        if ($storeId) {
            $payableQuery->where('pos_toko_id', $storeId);
        }
        $payable = $payableQuery->sum(DB::raw('total_harga - paid_amount'));

        return view('reports.financial', compact(
            // Core Metrics
            'totalRevenue',
            'totalHPP',
            'grossProfit',
            'grossMargin',
            'totalOperatingExpenses',
            'netProfit',
            'netMargin',
            
            // Cash Flow
            'cashIn',
            'cashOut',
            'freeCashFlow',
            'paymentBreakdown',
            'cashBalancePerOutlet',
            'receivable',
            'payable',
            
            // Details
            'itemDetails',
            'expenses',
            'expensesByType',
            'totalSalesCount',
            
            // Filters
            'period',
            'startDate',
            'endDate',
            'storeId',
            'merkId',
            'stores',
            'merks'
        ));
    }

    // Export Methods
    public function exportCustomers(Request $request)
    {
        $sortBy = $request->get('sort_by', 'name');

        $query = PosPelanggan::with(['transaksi']);

        if ($sortBy == 'purchases') {
            $query->withCount('transaksi')->orderBy('transaksi_count', 'desc');
        } elseif ($sortBy == 'value') {
            $query->withSum('transaksi', 'total_harga')->orderBy('transaksi_sum_total_harga', 'desc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $customers = $query->get();

        $summary = [
            'totalCustomers' => $customers->count(),
            'totalPurchases' => $customers->sum(function($c) { return $c->transaksi->count(); }),
            'totalValue' => $customers->sum(function($c) { return $c->transaksi->sum('total_harga'); }),
            'averageValue' => 0
        ];
        
        $summary['averageValue'] = $summary['totalCustomers'] > 0 
            ? $summary['totalValue'] / $summary['totalCustomers'] 
            : 0;

        return Excel::download(
            new CustomersReportExport($customers, $sortBy, $summary),
            'Laporan_Pelanggan_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportSales(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $storeId = $request->get('store_id');

        $query = PosTransaksi::with(['toko', 'items.produk', 'pelanggan']);

        // Apply date filters
        if ($period == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'totalSales' => $transactions->sum('total_harga'),
            'totalTransactions' => $transactions->count(),
            'totalItems' => $transactions->sum(function($t) { return $t->items->sum('quantity'); }),
            'averageTransaction' => 0
        ];
        
        $summary['averageTransaction'] = $summary['totalTransactions'] > 0 
            ? $summary['totalSales'] / $summary['totalTransactions'] 
            : 0;

        return Excel::download(
            new SalesReportExport($transactions, $summary, $period),
            'Laporan_Penjualan_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportTradeIn(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $storeId = $request->get('store_id');

        $query = PosTukarTambah::with(['pelanggan', 'produkMasuk', 'produkKeluar', 'toko', 'transaksiPenjualan', 'transaksiPembelian']);

        // Apply date filters
        if ($period == 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'year') {
            $query->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        }

        $tradeIns = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'totalTradeIns' => $tradeIns->count(),
            'totalTradeInValue' => $tradeIns->sum(function($tt) {
                return $tt->transaksiPembelian ? $tt->transaksiPembelian->total_harga : 0;
            }),
            'totalAdditionalPayment' => $tradeIns->sum(function($tt) {
                return $tt->transaksiPenjualan ? $tt->transaksiPenjualan->total_harga : 0;
            }),
            'totalValue' => 0
        ];
        
        $summary['totalValue'] = $summary['totalTradeInValue'] + $summary['totalAdditionalPayment'];

        return Excel::download(
            new TradeInReportExport($tradeIns, $summary, $period),
            'Laporan_Tukar_Tambah_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportProducts(Request $request)
    {
        $categoryFilter = $request->get('category');
        $brandFilter = $request->get('brand');

        $query = PosProduk::with(['merk', 'stok']);

        if ($brandFilter) {
            $query->where('pos_produk_merk_id', $brandFilter);
        }

        $products = $query->orderBy('nama')->get();

        $summary = [
            'totalProducts' => $products->count(),
            'totalStock' => $products->sum(function($p) { return $p->stok->sum('stok'); }),
            'totalValue' => $products->sum(function($p) {
                $totalStok = $p->stok->sum('stok');
                return $totalStok * $p->harga_jual;
            })
        ];

        return Excel::download(
            new ProductsReportExport($products, $summary),
            'Laporan_Produk_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportStock(Request $request)
    {
        $storeId = $request->get('store_id');
        $lowStockOnly = $request->get('low_stock', false);

        $query = ProdukStok::with(['produk', 'toko']);

        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        } else {
            $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
            $ownerId = $owner ? $owner->id : 0;
            
            $query->whereHas('toko', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });
        }

        if ($lowStockOnly) {
            $query->where('stok', '<=', 5);
        }

        $stocks = $query->orderBy('stok', 'asc')->get();

        $summary = [
            'totalItems' => $stocks->count(),
            'totalStock' => $stocks->sum('stok'),
            'lowStockItems' => $stocks->where('stok', '<=', 5)->count(),
            'outOfStock' => $stocks->where('stok', 0)->count()
        ];

        return Excel::download(
            new StockReportExport($stocks, $summary),
            'Laporan_Stok_' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportFinancial(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $storeId = $request->get('store_id');
        $merkId = $request->get('merk_id');

        // Get owner_id from logged in user
        $owner = Owner::where('pengguna_id', auth()->user()->id)->first();
        $ownerId = $owner ? $owner->id : 0;

        // Base query for transactions
        $transactionQuery = PosTransaksi::with(['items.produk.merk', 'toko'])
            ->whereHas('toko', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        // Apply date filters
        if ($period == 'today') {
            $transactionQuery->whereDate('created_at', Carbon::today());
        } elseif ($period == 'week') {
            $transactionQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $transactionQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'year') {
            $transactionQuery->whereYear('created_at', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $transactionQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($storeId) {
            $transactionQuery->where('pos_toko_id', $storeId);
        }

        if ($merkId) {
            $transactionQuery->whereHas('items.produk', function($q) use ($merkId) {
                $q->where('pos_produk_merk_id', $merkId);
            });
        }

        $transactions = $transactionQuery->get();

        // Calculate HPP and Revenue
        $revenue = 0;
        $hpp = 0;
        $detailPerItem = [];

        foreach ($transactions as $transaction) {
            foreach ($transaction->items as $item) {
                $produk = $item->produk;
                $quantity = $item->quantity;
                $hargaJual = $item->harga_jual ?? ($produk ? $produk->harga_jual : 0);
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

        // Operating Expenses
        $expenseQuery = PosExpense::byOwner($ownerId);
        
        if ($period == 'today') {
            $expenseQuery->whereDate('expense_date', Carbon::today());
        } elseif ($period == 'week') {
            $expenseQuery->whereBetween('expense_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period == 'month') {
            $expenseQuery->whereMonth('expense_date', Carbon::now()->month)->whereYear('expense_date', Carbon::now()->year);
        } elseif ($period == 'year') {
            $expenseQuery->whereYear('expense_date', Carbon::now()->year);
        } elseif ($period == 'custom' && $startDate && $endDate) {
            $expenseQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }

        if ($storeId) {
            $expenseQuery->where('pos_toko_id', $storeId);
        }

        $operatingExpenses = $expenseQuery->sum('amount');
        $recentExpenses = $expenseQuery->latest('expense_date')->limit(10)->get();

        // Core Metrics
        $grossProfit = $revenue - $hpp;
        $grossMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
        $netProfit = $grossProfit - $operatingExpenses;
        $netMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        // Cash Flow
        $cashIn = $revenue;
        $cashOut = $hpp + $operatingExpenses;
        $freeCashFlow = $cashIn - $cashOut;

        // Payment Methods
        $paymentMethods = $transactions->groupBy('metode_pembayaran')->map(function($group) {
            return $group->sum('total_harga');
        })->toArray();

        // Receivable
        $receivable = $transactions->where('payment_status', '!=', 'paid')->sum(function($t) {
            return $t->total_harga - ($t->paid_amount ?? 0);
        });

        $data = [
            'revenue' => $revenue,
            'hpp' => $hpp,
            'grossProfit' => $grossProfit,
            'grossMargin' => $grossMargin,
            'operatingExpenses' => $operatingExpenses,
            'netProfit' => $netProfit,
            'netMargin' => $netMargin,
            'cashIn' => $cashIn,
            'cashOut' => $cashOut,
            'freeCashFlow' => $freeCashFlow,
            'receivable' => $receivable,
            'paymentMethods' => $paymentMethods,
            'detailPerItem' => $detailPerItem,
            'recentExpenses' => $recentExpenses
        ];

        return Excel::download(
            new FinancialReportExport($data),
            'Laporan_Keuangan_' . date('Y-m-d') . '.xlsx'
        );
    }
}
