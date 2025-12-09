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

    // Owner & Admin Routes - User Management
    Route::middleware(['role:OWNER,ADMIN'])->group(function () {
        Route::resource('user', UserController::class);
    });

    // Owner Only Routes
    Route::middleware(['role:OWNER'])->group(function () {
        Route::resource('tradein', \App\Http\Controllers\TradeInController::class);
        Route::resource('category', \App\Http\Controllers\CategoryController::class);
        Route::resource('storages', \App\Http\Controllers\StorageController::class);
        Route::resource('colors', \App\Http\Controllers\ColorController::class);
        // Product Names master with dedicated table
        Route::resource('product-name', \App\Http\Controllers\ProductNameController::class);
        Route::get('chat-analisis', [\App\Http\Controllers\ChatAnalysisController::class, 'index'])->name('chat.index');
        Route::post('chat-analisis/ask', [\App\Http\Controllers\ChatAnalysisController::class, 'ask'])->name('chat.ask');
    });

    // Admin & Owner Routes (shared access)
    Route::get('api/products/search', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
    Route::resource('transaction', \App\Http\Controllers\TransactionController::class);
    Route::get('transaction/{transaction}/print', [\App\Http\Controllers\TransactionController::class, 'print'])->name('transaction.print');
    Route::get('transaction/{transaction}/invoice', [\App\Http\Controllers\TransactionController::class, 'invoice'])->name('transaction.invoice');
    Route::resource('customer', \App\Http\Controllers\CustomerController::class);

    // Admin & Owner access to Product management
    Route::middleware(['role:OWNER,ADMIN'])->group(function () {
        Route::resource('product', \App\Http\Controllers\ProductController::class)->except(['show']);
    });

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
