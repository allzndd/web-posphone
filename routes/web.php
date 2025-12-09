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

    // Admin & Owner Routes - POS Transactions & Customers
    Route::middleware(['role:OWNER,ADMIN'])->group(function () {
        // POS Users (pos_pengguna) - Karyawan toko
        Route::resource('pos-pengguna', \App\Http\Controllers\PosPenggunaController::class);
        
        // Transactions (pos_transaksi)
        Route::resource('transaksi', \App\Http\Controllers\TransaksiController::class);
        Route::get('transaksi/{transaksi}/print', [\App\Http\Controllers\TransaksiController::class, 'print'])->name('transaksi.print');
        Route::get('transaksi/{transaksi}/invoice', [\App\Http\Controllers\TransaksiController::class, 'invoice'])->name('transaksi.invoice');
        
        // Returns (pos_retur)
        Route::resource('retur', \App\Http\Controllers\ReturController::class);
        
        // Customers (pos_pelanggan)
        Route::resource('pelanggan', \App\Http\Controllers\PelangganController::class);
    });

    // Owner Only Routes - POS Management
    Route::middleware(['role:OWNER'])->group(function () {
        // POS Roles (pos_role)
        Route::resource('pos-role', \App\Http\Controllers\PosRoleController::class);
        
        // Stores (pos_toko)
        Route::resource('toko', \App\Http\Controllers\TokoController::class);
        
        // Products (pos_produk)
        Route::resource('produk', \App\Http\Controllers\ProdukController::class);
        
        // Product Brands (pos_produk_merk)
        Route::resource('produk-merk', \App\Http\Controllers\ProdukMerkController::class);
        
        // Stock Management (pos_produk_stok)
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
    Route::get('api/products/search', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
    Route::get('api/produk/search', [\App\Http\Controllers\Api\ProdukController::class, 'search'])->name('produk.search');

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
