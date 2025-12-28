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
    
    // Supplier API Routes
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    // ========== PRODUCT MENU API ROUTES ==========
    
    // All Products Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [AllProductController::class, 'index']);
        Route::post('/', [AllProductController::class, 'store']);
        Route::get('/{id}', [AllProductController::class, 'show']);
        Route::put('/{id}', [AllProductController::class, 'update']);
        Route::delete('/{id}', [AllProductController::class, 'destroy']);
    });

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

