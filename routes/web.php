<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManageProfilController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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
    if (auth()->check()) {
        // Check if user's email is verified before redirecting to dashboard
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        return redirect()->route('dashboard');
    }
    return view('landing_page.index');
})->name('landing');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    // Notice page - tell user to verify their email
    Route::get('/email/verify', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    // Verify email with signed URL
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        // Mark email as verified
        $request->user()->markEmailAsVerified();
        
        return redirect()->route('dashboard')->with('success', 'Email Anda telah berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    // Resend verification email
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Link verifikasi telah dikirim ulang ke email Anda!');
    })->middleware(['throttle:3,1'])->name('verification.send');
});

Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('home', '/dashboard');
    Route::get('dashboard-general-dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
    
    // Subscription expired page (accessible without subscription check)
    Route::get('subscription-expired', function () {
        return view('pembayaran.expired');
    })->name('pembayaran.expired');
    
    // Download Reports (accessible by all authenticated users)
    Route::get('dashboard/download-report', [\App\Http\Controllers\DashboardController::class, 'downloadFinancialReport'])->name('dashboard.download-report');
    Route::get('transaksi/download-report', [\App\Http\Controllers\TransaksiController::class, 'downloadReport'])->name('transaksi.download-report');

    // Admin & Owner Routes - POS Transactions & Customers (with subscription check)
    Route::middleware(['role:OWNER,ADMIN', 'subscription'])->group(function () {
        // POS Users (pos_pengguna) - Karyawan toko - Custom bulk delete must come before resource
        Route::delete('pos-pengguna/bulk-destroy', [\App\Http\Controllers\PosPenggunaController::class, 'bulkDestroy'])->name('pos-pengguna.bulk-destroy');
        Route::resource('pos-pengguna', \App\Http\Controllers\PosPenggunaController::class);
        
        // Incoming Transactions (Sales) - Must come before resource route
        Route::prefix('transaksi/masuk')->name('transaksi.masuk.')->group(function () {
            Route::delete('bulk-destroy', [\App\Http\Controllers\TransaksiController::class, 'bulkDestroyMasuk'])->name('bulk-destroy');
            Route::get('/', [\App\Http\Controllers\TransaksiController::class, 'indexMasuk'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TransaksiController::class, 'createMasuk'])->name('create');
            Route::post('/', [\App\Http\Controllers\TransaksiController::class, 'storeMasuk'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\TransaksiController::class, 'editMasuk'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\TransaksiController::class, 'updateMasuk'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\TransaksiController::class, 'destroyMasuk'])->name('destroy');
            Route::get('/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'printMasuk'])->name('print');
        });
        
        // Outgoing Transactions (Purchases) - Must come before resource route
        Route::prefix('transaksi/keluar')->name('transaksi.keluar.')->group(function () {
            Route::delete('bulk-destroy', [\App\Http\Controllers\TransaksiController::class, 'bulkDestroyKeluar'])->name('bulk-destroy');
            Route::get('/', [\App\Http\Controllers\TransaksiController::class, 'indexKeluar'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TransaksiController::class, 'createKeluar'])->name('create');
            Route::post('/', [\App\Http\Controllers\TransaksiController::class, 'storeKeluar'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\TransaksiController::class, 'editKeluar'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\TransaksiController::class, 'updateKeluar'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\TransaksiController::class, 'destroyKeluar'])->name('destroy');
            Route::get('/{id}/print', [\App\Http\Controllers\TransaksiController::class, 'printKeluar'])->name('print');
        });
        
        // Expenses - Must come before resource route
        Route::prefix('expense')->name('expense.')->group(function () {
            Route::delete('bulk-destroy', [\App\Http\Controllers\ExpenseController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::get('/', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\ExpenseController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('destroy');
        });
        
        // Transactions (pos_transaksi) - General resource route
        Route::resource('transaksi', \App\Http\Controllers\TransaksiController::class);
        
        // Returns (pos_retur)
        // Route::resource('retur', \App\Http\Controllers\ReturController::class);
        
        // Customers (pos_pelanggan)
        Route::delete('customer/bulk-destroy', [\App\Http\Controllers\CustomerController::class, 'bulkDestroy'])->name('customer.bulk-destroy');
        Route::resource('customer', \App\Http\Controllers\CustomerController::class);
        Route::delete('pelanggan/bulk-destroy', [\App\Http\Controllers\PelangganController::class, 'bulkDestroy'])->name('pelanggan.bulk-destroy');
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
            Route::get('/expense', [\App\Http\Controllers\ReportController::class, 'expense'])->name('expense');
            Route::get('/expense/export', [\App\Http\Controllers\ReportController::class, 'exportExpense'])->name('expense.export');
        });
    });

    // Owner Only Routes - POS Management (with subscription check)
    Route::middleware(['role:OWNER', 'subscription'])->group(function () {
        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
        Route::get('/email/verify/{token}', [\App\Http\Controllers\SettingsController::class, 'verifyNewEmail'])->name('email.verify');
        
        // Trade-In (pos_tukar_tambah) - OWNER ONLY
        Route::resource('tukar-tambah', \App\Http\Controllers\TukarTambahController::class);
        
        // POS Roles (pos_role) - Custom bulk delete must come before resource
        Route::delete('pos-role/bulk-destroy', [\App\Http\Controllers\PosRoleController::class, 'bulkDestroy'])->name('pos-role.bulk-destroy');
        Route::resource('pos-role', \App\Http\Controllers\PosRoleController::class);
        
        // Stores (pos_toko) - Custom bulk delete must come before resource
        Route::delete('toko/bulk-destroy', [\App\Http\Controllers\TokoController::class, 'bulkDestroy'])->name('toko.bulk-destroy');
        Route::resource('toko', \App\Http\Controllers\TokoController::class);
        
        // Products (pos_produk) - Custom bulk delete must come before resource
        Route::delete('produk/bulk-destroy', [\App\Http\Controllers\ProdukController::class, 'bulkDestroy'])->name('produk.bulk-destroy');
        Route::resource('produk', \App\Http\Controllers\ProdukController::class);
        
        // Stock Management (pos_produk_stok) - Custom bulk delete must come before resource
        Route::delete('produk-stok/bulk-destroy', [\App\Http\Controllers\ProdukStokController::class, 'bulkDestroy'])->name('produk-stok.bulk-destroy');
        Route::resource('produk-stok', \App\Http\Controllers\ProdukStokController::class);
        
        // Stock History (pos_log_stok)
        Route::get('log-stok', [\App\Http\Controllers\LogStokController::class, 'index'])->name('log-stok.index');
        
        // Suppliers (pos_supplier) - Custom bulk delete must come before resource
        Route::delete('supplier/bulk-destroy', [\App\Http\Controllers\SupplierController::class, 'bulkDestroy'])->name('supplier.bulk-destroy');
        Route::resource('supplier', \App\Http\Controllers\SupplierController::class);
        
        // Services (pos_service) - Custom bulk delete must come before resource
        Route::delete('service/bulk-destroy', [\App\Http\Controllers\ServiceController::class, 'bulkDestroy'])->name('service.bulk-destroy');
        Route::resource('service', \App\Http\Controllers\ServiceController::class);
        
        // Chat Analisis
        Route::get('chat-analisis', [\App\Http\Controllers\ChatAnalysisController::class, 'index'])->name('chat.index');
        Route::post('chat-analisis/ask', [\App\Http\Controllers\ChatAnalysisController::class, 'ask'])->name('chat.ask');
    });

    // Data Master - Penyimpanan, Warna, RAM, Product Merk (OWNER & SUPERADMIN accessible, subscription check for OWNER)
    Route::middleware(['role:OWNER,SUPERADMIN', 'subscription'])->group(function () {
        Route::delete('pos-penyimpanan/bulk-destroy', [\App\Http\Controllers\PosPenyimpananController::class, 'bulkDestroy'])->name('pos-penyimpanan.bulk-destroy');
        Route::resource('pos-penyimpanan', \App\Http\Controllers\PosPenyimpananController::class);
        Route::delete('pos-warna/bulk-destroy', [\App\Http\Controllers\PosWarnaController::class, 'bulkDestroy'])->name('pos-warna.bulk-destroy');
        Route::resource('pos-warna', \App\Http\Controllers\PosWarnaController::class);
        Route::delete('pos-ram/bulk-destroy', [\App\Http\Controllers\PosRamController::class, 'bulkDestroy'])->name('pos-ram.bulk-destroy');
        Route::resource('pos-ram', \App\Http\Controllers\PosRamController::class);
        Route::delete('pos-produk-merk/bulk-destroy', [\App\Http\Controllers\PosProdukMerkController::class, 'bulkDestroy'])->name('pos-produk-merk.bulk-destroy');
        Route::post('api/pos-produk-merk/quick-store', [\App\Http\Controllers\PosProdukMerkController::class, 'quickStore'])->name('pos-produk-merk.quick-store');
        Route::resource('pos-produk-merk', \App\Http\Controllers\PosProdukMerkController::class);
        Route::delete('pos-kategori-expense/bulk-destroy', [\App\Http\Controllers\PosKategoriExpenseController::class, 'bulkDestroy'])->name('pos-kategori-expense.bulk-destroy');
        Route::resource('pos-kategori-expense', \App\Http\Controllers\PosKategoriExpenseController::class);
    });

    // API Routes for AJAX (accessible by OWNER & ADMIN with subscription)
    Route::middleware(['role:OWNER,ADMIN', 'subscription'])->group(function () {
        Route::post('api/produk/quick-store', [\App\Http\Controllers\ProdukController::class, 'quickStore'])->name('produk.quick-store');
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

        // Permissions Management
        Route::resource('permissions', \App\Http\Controllers\PermissionController::class);
        Route::delete('permissions/module/destroy', [\App\Http\Controllers\PermissionController::class, 'destroyModule'])->name('permissions.module.destroy');
        Route::delete('permissions/bulk-delete', [\App\Http\Controllers\PermissionController::class, 'bulkDelete'])->name('permissions.bulk-delete');

        // App Versions
        Route::delete('app-version/bulk-destroy', [\App\Http\Controllers\AppVersionController::class, 'bulkDestroy'])->name('app-version.bulk-destroy');
        Route::resource('app-version', \App\Http\Controllers\AppVersionController::class);

        // Manage Profile - Contact Admin
        Route::get('manage-profil/contact-admin', [ManageProfilController::class, 'contactAdmin'])->name('manage-profil.contact-admin.index');
    });
});
