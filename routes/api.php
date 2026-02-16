<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\AllProductController;
use App\Http\Controllers\Api\ProductBrandController;
use App\Http\Controllers\Api\StockManagementController;
use App\Http\Controllers\Api\StockHistoryController;
use App\Http\Controllers\Api\NewProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\IncomingTransactionController;
use App\Http\Controllers\Api\OutgoingTransactionController;
use App\Http\Controllers\Api\AllTransactionController;
use App\Http\Controllers\Api\SemuaLaporanController;
use App\Http\Controllers\Api\LaporanPenjualanController;
use App\Http\Controllers\Api\LaporanTukarTambahController;
use App\Http\Controllers\Api\LaporanProdukController;
use App\Http\Controllers\Api\LaporanStokController;
use App\Http\Controllers\Api\LaporanPelangganController;
use App\Http\Controllers\Api\RingkasanKeuanganController;
use App\Http\Controllers\Api\TradeInController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\StorageController;
use App\Http\Controllers\Api\RamController;
use App\Http\Controllers\Api\ExpenseTransactionController;
use App\Http\Controllers\Api\ProductSearchController;
use App\Models\Langganan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth Routes (Public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    
    // Product Search Routes
    Route::get('/products/search', [ProductSearchController::class, 'search']);
    
    // User Management Routes (Owner only)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::post('/', [UserManagementController::class, 'store']);
        Route::get('/{id}', [UserManagementController::class, 'show']);
        Route::put('/{id}', [UserManagementController::class, 'update']);
        Route::delete('/{id}', [UserManagementController::class, 'destroy']);
    });
    
    // Dashboard API Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/sales-chart', [DashboardController::class, 'getSalesChart']);
        Route::get('/top-products', [DashboardController::class, 'getTopProducts']);
        Route::get('/low-stock', [DashboardController::class, 'getLowStock']);
        Route::get('/recent-transactions', [DashboardController::class, 'getRecentTransactions']);
    });
    
    // Supplier API Routes
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    // Stores/Tokos API Routes
    Route::get('/stores', [\App\Http\Controllers\Api\StoreController::class, 'index']);
    Route::post('/stores', [\App\Http\Controllers\Api\StoreController::class, 'store']);
    Route::get('/stores/{id}', [\App\Http\Controllers\Api\StoreController::class, 'show']);
    Route::put('/stores/{id}', [\App\Http\Controllers\Api\StoreController::class, 'update']);
    Route::delete('/stores/{id}', [\App\Http\Controllers\Api\StoreController::class, 'destroy']);

    // Expense Categories API Routes
    Route::get('/expense-categories', [ExpenseCategoryController::class, 'index']);
    Route::post('/expense-categories', [ExpenseCategoryController::class, 'store']);
    Route::get('/expense-categories/{id}', [ExpenseCategoryController::class, 'show']);
    Route::put('/expense-categories/{id}', [ExpenseCategoryController::class, 'update']);
    Route::delete('/expense-categories/{id}', [ExpenseCategoryController::class, 'destroy']);

    // Colors API Routes
    Route::get('/colors', [ColorController::class, 'index']);
    Route::post('/colors', [ColorController::class, 'store']);
    Route::get('/colors/{id}', [ColorController::class, 'show']);
    Route::put('/colors/{id}', [ColorController::class, 'update']);
    Route::delete('/colors/{id}', [ColorController::class, 'destroy']);

    // Storage API Routes
    Route::get('/storages', [StorageController::class, 'index']);
    Route::post('/storages', [StorageController::class, 'store']);
    Route::get('/storages/{id}', [StorageController::class, 'show']);
    Route::put('/storages/{id}', [StorageController::class, 'update']);
    Route::delete('/storages/{id}', [StorageController::class, 'destroy']);

    // RAM API Routes
    Route::get('/rams', [RamController::class, 'index']);
    Route::post('/rams', [RamController::class, 'store']);
    Route::get('/rams/{id}', [RamController::class, 'show']);
    Route::put('/rams/{id}', [RamController::class, 'update']);
    Route::delete('/rams/{id}', [RamController::class, 'destroy']);

    // Expense Transaction API Routes
    Route::get('/expense-transactions', [ExpenseTransactionController::class, 'index']);
    Route::post('/expense-transactions', [ExpenseTransactionController::class, 'store']);
    Route::get('/expense-transactions/{id}', [ExpenseTransactionController::class, 'show']);
    Route::put('/expense-transactions/{id}', [ExpenseTransactionController::class, 'update']);
    Route::delete('/expense-transactions/{id}', [ExpenseTransactionController::class, 'destroy']);

    // Customers API Routes
    Route::get('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'index']);
    Route::post('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'store']);
    Route::get('/customers/stats', [\App\Http\Controllers\Api\CustomerController::class, 'stats']);
    Route::get('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'show']);
    Route::put('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'destroy']);

    // ========== PRODUCT MENU API ROUTES ==========
    
    // All Products Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [AllProductController::class, 'index']);
        Route::post('/', [AllProductController::class, 'store']);
        Route::get('/{id}', [AllProductController::class, 'show']);
        Route::put('/{id}', [AllProductController::class, 'update']);
        Route::delete('/{id}', [AllProductController::class, 'destroy']);
    });

    // New Product (mirrors web "New Product" form)
    Route::post('/new-product', [NewProductController::class, 'store']);

    // Product Brands Routes
    Route::prefix('product-brands')->group(function () {
        Route::get('/', [ProductBrandController::class, 'index']);
        Route::post('/', [ProductBrandController::class, 'store']);
        Route::get('/{id}', [ProductBrandController::class, 'show']);
        Route::put('/{id}', [ProductBrandController::class, 'update']);
        Route::delete('/{id}', [ProductBrandController::class, 'destroy']);
    });

    // Stock Management Routes
    Route::prefix('stock-management')->group(function () {
        Route::get('/', [StockManagementController::class, 'index']);
        Route::get('/summary', [StockManagementController::class, 'summary']);
        Route::put('/{id}', [StockManagementController::class, 'update']);
        Route::post('/adjust', [StockManagementController::class, 'adjust']);
    });

    // Stock History Routes
    Route::prefix('stock-history')->group(function () {
        Route::get('/', [StockHistoryController::class, 'index']);
        Route::get('/summary', [StockHistoryController::class, 'summary']);
        Route::get('/{id}', [StockHistoryController::class, 'show']);
        Route::get('/product/{productId}', [StockHistoryController::class, 'byProduct']);
    });

    // Service Routes
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/{id}', [ServiceController::class, 'show']);
        Route::put('/{id}', [ServiceController::class, 'update']);
        Route::delete('/{id}', [ServiceController::class, 'destroy']);
    });

    // Trade-In Routes
    Route::prefix('trade-in')->group(function () {
        Route::get('/customers', [TradeInController::class, 'getCustomers']);
        Route::get('/products', [TradeInController::class, 'getProducts']);
        Route::get('/', [TradeInController::class, 'index']);
        Route::post('/', [TradeInController::class, 'store']);
        Route::get('/{id}', [TradeInController::class, 'show']);
        Route::put('/{id}', [TradeInController::class, 'update']);
        Route::delete('/{id}', [TradeInController::class, 'destroy']);
    });

    // Reports API Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [SemuaLaporanController::class, 'index']);
        Route::get('/sales', [LaporanPenjualanController::class, 'index']);
        Route::get('/sales/export', [LaporanPenjualanController::class, 'exportExcel']);
        Route::get('/stores', [LaporanPenjualanController::class, 'getStores']);
        Route::get('/trade-in', [LaporanTukarTambahController::class, 'index']);
        Route::get('/trade-in/stores', [LaporanTukarTambahController::class, 'getStores']);
        Route::get('/trade-in/export', [LaporanTukarTambahController::class, 'exportExcel']);
        Route::get('/products', [LaporanProdukController::class, 'index']);
        Route::get('/products/summary', [LaporanProdukController::class, 'getSummary']);
        Route::get('/products/stores', [LaporanProdukController::class, 'getStores']);
        Route::get('/products/export', [LaporanProdukController::class, 'exportExcel']);
        Route::get('/stock', [LaporanStokController::class, 'index']);
        Route::get('/stock/export', [LaporanStokController::class, 'exportExcel']);
        Route::get('/customers', [LaporanPelangganController::class, 'index']);
        Route::get('/customers/summary', [LaporanPelangganController::class, 'getSummary']);
        Route::get('/customers/export', [LaporanPelangganController::class, 'exportExcel']);
        Route::get('/financial', [RingkasanKeuanganController::class, 'index']);
        Route::get('/financial/summary', [RingkasanKeuanganController::class, 'getSummary']);
        Route::get('/financial/export', [RingkasanKeuanganController::class, 'exportExcel']);
        Route::get('/financial/detail-per-item', [RingkasanKeuanganController::class, 'getDetailPerItem']);
        Route::get('/financial/operating-expenses', [RingkasanKeuanganController::class, 'getOperatingExpenses']);
    });

    // All Transactions Routes
    Route::prefix('transactions')->group(function () {
        Route::get('/', [AllTransactionController::class, 'index']);
        Route::get('/summary', [AllTransactionController::class, 'summary']);
        Route::get('/{id}', [AllTransactionController::class, 'show'])->whereNumber('id');
    });

    // Incoming Transactions Routes (Sales)
    Route::prefix('transactions/incoming')->group(function () {
        Route::get('/', [IncomingTransactionController::class, 'index']);
        Route::post('/', [IncomingTransactionController::class, 'store']);
        Route::get('/summary', [IncomingTransactionController::class, 'summary']);
        Route::get('/{id}', [IncomingTransactionController::class, 'show']);
        Route::put('/{id}', [IncomingTransactionController::class, 'update']);
        Route::delete('/{id}', [IncomingTransactionController::class, 'destroy']);
    });

    // Outgoing Transactions Routes (Purchases)
    Route::prefix('transactions/outgoing')->group(function () {
        Route::get('/', [OutgoingTransactionController::class, 'index']);
        Route::post('/', [OutgoingTransactionController::class, 'store']);
        Route::get('/summary', [OutgoingTransactionController::class, 'summary']);
        Route::get('/{id}', [OutgoingTransactionController::class, 'show']);
        Route::put('/{id}', [OutgoingTransactionController::class, 'update']);
        Route::delete('/{id}', [OutgoingTransactionController::class, 'destroy']);
    });
});

// Product Search API (with web middleware for session auth)
Route::middleware('web')->get('/products/search', [ProductController::class, 'search'])->name('api.products.search');

// Get langganan by owner
Route::middleware('web')->get('/langganan/owner/{ownerId}', function ($ownerId) {
    return Langganan::with('tipeLayanan')
        ->where('owner_id', $ownerId)
        ->get()
        ->map(function($langganan) {
            return [
                'id' => $langganan->id,
                'started_date' => $langganan->started_date->format('d/m/Y'),
                'end_date' => $langganan->end_date->format('d/m/Y'),
                'tipe_layanan' => [
                    'nama' => $langganan->tipeLayanan->nama,
                    'harga' => $langganan->tipeLayanan->harga,
                ],
            ];
        });
});

