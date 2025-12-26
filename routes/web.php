<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Landing Page
Route::get('/', function () {
    return view('landing_page.index');
})->name('landing');

Route::middleware(['auth'])->group(function () {
    Route::get('home', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard-general-dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
    
    // Download Reports (accessible by all authenticated users)
    Route::get('dashboard/download-report', [\App\Http\Controllers\DashboardController::class, 'downloadFinancialReport'])->name('dashboard.download-report');
    Route::get('transaksi/download-report', [\App\Http\Controllers\TransaksiController::class, 'downloadReport'])->name('transaksi.download-report');

    // Admin & Owner Routes - POS Transactions & Customers
    Route::middleware(['role:OWNER,ADMIN'])->group(function () {
        // POS Users (pos_pengguna) - Karyawan toko
        Route::resource('pos-pengguna', \App\Http\Controllers\PosPenggunaController::class);
        
        // Incoming Transactions (Sales) - Must come before resource route
        Route::prefix('transaksi/masuk')->name('transaksi.masuk.')->group(function () {
            Route::get('/', [\App\Http\Controllers\TransaksiController::class, 'indexMasuk'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TransaksiController::class, 'createMasuk'])->name('create');
            Route::post('/', [\App\Http\Controllers\TransaksiController::class, 'storeMasuk'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\TransaksiController::class, 'editMasuk'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\TransaksiController::class, 'updateMasuk'])->name('update');
        });
        
        // Outgoing Transactions (Purchases) - Must come before resource route
        Route::prefix('transaksi/keluar')->name('transaksi.keluar.')->group(function () {
            Route::get('/', [\App\Http\Controllers\TransaksiController::class, 'indexKeluar'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TransaksiController::class, 'createKeluar'])->name('create');
            Route::post('/', [\App\Http\Controllers\TransaksiController::class, 'storeKeluar'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\TransaksiController::class, 'editKeluar'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\TransaksiController::class, 'updateKeluar'])->name('update');
        });
        
        // Transactions (pos_transaksi) - General resource route
        Route::resource('transaksi', \App\Http\Controllers\TransaksiController::class);
        
        // Returns (pos_retur)
        // Route::resource('retur', \App\Http\Controllers\ReturController::class);
        
        // Customers (pos_pelanggan)
        Route::resource('pelanggan', \App\Http\Controllers\PelangganController::class);
        
        // Reports - Laporan
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
            Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('sales');
            Route::get('/sales/export', [\App\Http\Controllers\ReportController::class, 'exportSales'])->name('sales.export');
            Route::get('/trade-in', [\App\Http\Controllers\ReportController::class, 'tradeIn'])->name('trade-in');
            Route::get('/trade-in/export', [\App\Http\Controllers\ReportController::class, 'exportTradeIn'])->name('trade-in.export');
            Route::get('/products', [\App\Http\Controllers\ReportController::class, 'products'])->name('products');
            Route::get('/products/export', [\App\Http\Controllers\ReportController::class, 'exportProducts'])->name('products.export');
            Route::get('/stock', [\App\Http\Controllers\ReportController::class, 'stock'])->name('stock');
            Route::get('/stock/export', [\App\Http\Controllers\ReportController::class, 'exportStock'])->name('stock.export');
            Route::get('/customers', [\App\Http\Controllers\ReportController::class, 'customers'])->name('customers');
            Route::get('/customers/export', [\App\Http\Controllers\ReportController::class, 'exportCustomers'])->name('customers.export');
            Route::get('/financial', [\App\Http\Controllers\ReportController::class, 'financial'])->name('financial');
            Route::get('/financial/export', [\App\Http\Controllers\ReportController::class, 'exportFinancial'])->name('financial.export');
        });
    });

    // Owner Only Routes - POS Management
    Route::middleware(['role:OWNER'])->group(function () {
        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        
        // Trade-In (pos_tukar_tambah) - OWNER ONLY
        Route::resource('tukar-tambah', \App\Http\Controllers\TukarTambahController::class);
        
        // POS Roles (pos_role)
        Route::resource('pos-role', \App\Http\Controllers\PosRoleController::class);
        
        // Stores (pos_toko)
        Route::resource('toko', \App\Http\Controllers\TokoController::class);
        
        // Products (pos_produk)
        Route::resource('produk', \App\Http\Controllers\ProdukController::class);
        
        // Product Brands (pos_produk_merk)
        Route::resource('pos-produk-merk', \App\Http\Controllers\PosProdukMerkController::class);
        
        // Stock Management (pos_produk_stok)
        Route::patch('produk-stok/{produkStok}/update-inline', [\App\Http\Controllers\ProdukStokController::class, 'updateInline'])->name('produk-stok.update-inline');
        Route::resource('produk-stok', \App\Http\Controllers\ProdukStokController::class);
        
        // Stock History (pos_log_stok)
        Route::get('log-stok', [\App\Http\Controllers\LogStokController::class, 'index'])->name('log-stok.index');
        
        // Suppliers (pos_supplier)
        Route::resource('supplier', \App\Http\Controllers\SupplierController::class);
        
        // Services (pos_service)
        Route::resource('service', \App\Http\Controllers\ServiceController::class);
        
        // Chat Analisis
        Route::get('chat-analisis', [\App\Http\Controllers\ChatAnalysisController::class, 'index'])->name('chat.index');
        Route::post('chat-analisis/ask', [\App\Http\Controllers\ChatAnalysisController::class, 'ask'])->name('chat.ask');
    });

    // API Routes for AJAX
    // Route::get('api/products/search', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
    // Route::get('api/produk/search', [\App\Http\Controllers\Api\ProdukController::class, 'search'])->name('produk.search');

    // Superadmin Only - Dashboard Superadmin
    Route::middleware(['role:SUPERADMIN'])->group(function () {
        Route::get('dashboard-superadmin', [\App\Http\Controllers\DashboardSuperadminController::class, 'index'])->name('dashboard-superadmin');
        
        // Menu Baru - Kelola Owner
        Route::resource('kelola-owner', \App\Http\Controllers\KelolaOwnerController::class);
        
        // Menu Baru - Layanan
        Route::resource('layanan', \App\Http\Controllers\LayananController::class);
        
        // Menu Baru - Paket Layanan
        Route::resource('paket-layanan', \App\Http\Controllers\PaketLayananController::class);
        
        // Menu Baru - Pembayaran
        Route::resource('pembayaran', \App\Http\Controllers\PembayaranController::class);
    });
});
