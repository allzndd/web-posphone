<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukStok;
use App\Exports\StockReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanStokController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $perPage = $request->get('per_page', 10);
            
            // Base query with eager loading - explicitly select produk fields including imei
            $query = ProdukStok::with(['produk:id,nama,imei,deskripsi,harga_jual', 'toko:id,nama'])
                ->where('pos_produk_stok.owner_id', $ownerId);

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_produk_stok.pos_toko_id', $request->pos_toko_id);
            }

            // Filter by stock status
            if ($request->filled('stock_filter')) {
                $stockFilter = $request->stock_filter;
                if ($stockFilter === 'low_stock') {
                    $query->where('pos_produk_stok.stok', '>', 0)
                          ->where('pos_produk_stok.stok', '<=', 5);
                } elseif ($stockFilter === 'out_of_stock') {
                    $query->where('pos_produk_stok.stok', '=', 0);
                }
                // 'all' doesn't need additional filter
            } elseif ($request->boolean('low_stock')) {
                // Backward compatibility
                $query->where('pos_produk_stok.stok', '<=', 5);
            }

            // Search by product name, IMEI, or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('imei', 'LIKE', "%{$search}%")
                      ->orWhere('deskripsi', 'LIKE', "%{$search}%");
                });
            }

            // Order by stock
            $query->orderBy('pos_produk_stok.stok', 'asc');

            $stocks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Laporan stok berhasil diambil',
                'data' => $stocks->items(),
                'pagination' => [
                    'current_page' => $stocks->currentPage(),
                    'per_page' => $stocks->perPage(),
                    'total' => $stocks->total(),
                    'last_page' => $stocks->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in LaporanStokController: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan stok: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            // Base query with eager loading
            $query = \App\Models\ProdukStok::with(['produk:id,nama,imei,harga_jual', 'toko:id,nama'])
                ->where('pos_produk_stok.owner_id', $ownerId);

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_produk_stok.pos_toko_id', $request->pos_toko_id);
            }

            // Filter by stock status
            if ($request->filled('stock_filter')) {
                $stockFilter = $request->stock_filter;
                if ($stockFilter === 'low_stock') {
                    $query->where('pos_produk_stok.stok', '>', 0)
                          ->where('pos_produk_stok.stok', '<=', 5);
                } elseif ($stockFilter === 'out_of_stock') {
                    $query->where('pos_produk_stok.stok', '=', 0);
                }
            }

            // Search by product name, IMEI, or description
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('produk', function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('imei', 'LIKE', "%{$search}%")
                      ->orWhere('deskripsi', 'LIKE', "%{$search}%");
                });
            }

            $stocks = $query->orderBy('pos_produk_stok.stok', 'asc')->get();

            // Calculate summary - use camelCase keys for Excel export
            $summary = [
                'totalItems' => $stocks->count(),
                'totalStock' => $stocks->sum('stok'),
                'lowStockItems' => $stocks->filter(function($item) {
                    return $item->stok > 0 && $item->stok <= 5;
                })->count(),
                'outOfStock' => $stocks->where('stok', 0)->count(),
            ];

            return \Excel::download(
                new \App\Exports\StockReportExport($stocks, $summary),
                'Laporan_Stok_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error in LaporanStokController exportExcel: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan stok: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
