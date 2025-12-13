<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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

