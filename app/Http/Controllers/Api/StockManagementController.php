<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukStok;
use App\Models\PosToko;
use App\Models\PosProduk;
use App\Models\LogStok;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    /**
     * Display stock for all products across stores
     */
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
            
            $query = ProdukStok::where('owner_id', $ownerId)
                ->with(['produk.merk', 'toko'])
                ->orderBy('id', 'desc');

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }

            // Filter by product
            if ($request->filled('pos_produk_id')) {
                $query->where('pos_produk_id', $request->pos_produk_id);
            }

            // Filter by product name
            if ($request->filled('nama_produk')) {
                $query->whereHas('produk', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama_produk . '%');
                });
            }

            // Filter by low stock
            if ($request->filled('low_stock') && $request->low_stock == 'true') {
                $query->where('stok', '<=', 5);
            }

            $stocks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data stok berhasil diambil',
                'data' => $stocks->items(),
                'pagination' => [
                    'current_page' => $stocks->currentPage(),
                    'per_page' => $stocks->perPage(),
                    'total' => $stocks->total(),
                    'last_page' => $stocks->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock summary
     */
    public function summary(Request $request)
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

            $totalProducts = PosProduk::where('owner_id', $ownerId)->count();
            $totalStock = ProdukStok::where('owner_id', $ownerId)->sum('stok');
            $lowStockProducts = ProdukStok::where('owner_id', $ownerId)
                ->where('stok', '<=', 5)
                ->distinct('pos_produk_id')
                ->count('pos_produk_id');
            $outOfStock = ProdukStok::where('owner_id', $ownerId)
                ->where('stok', 0)
                ->distinct('pos_produk_id')
                ->count('pos_produk_id');

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan stok berhasil diambil',
                'data' => [
                    'total_products' => $totalProducts,
                    'total_stock' => $totalStock,
                    'low_stock_products' => $lowStockProducts,
                    'out_of_stock' => $outOfStock,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update stock for a product in a store
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'stok' => 'required|integer|min:0',
                'tipe' => 'required|in:masuk,keluar,adjustment',
                'keterangan' => 'nullable|string',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $stock = ProdukStok::where('owner_id', $ownerId)->findOrFail($id);
            $stokSebelum = $stock->stok;
            $stokBaru = $validated['stok'];

            // Calculate change
            $perubahan = $stokBaru - $stokSebelum;

            // Update stock
            $stock->update(['stok' => $stokBaru]);

            // Create log
            LogStok::create([
                'owner_id' => $ownerId,
                'pos_produk_id' => $stock->pos_produk_id,
                'pos_toko_id' => $stock->pos_toko_id,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokBaru,
                'perubahan' => $perubahan,
                'tipe' => $validated['tipe'],
                'referensi' => 'Update Stok Manual',
                'keterangan' => $validated['keterangan'] ?? 'Update stok melalui API',
                'pos_pengguna_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diupdate',
                'data' => $stock->load(['produk.merk', 'toko']),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust stock (increase or decrease)
     */
    public function adjust(Request $request)
    {
        try {
            $validated = $request->validate([
                'pos_produk_id' => 'required|exists:pos_produk,id',
                'pos_toko_id' => 'required|exists:pos_toko,id',
                'jumlah' => 'required|integer',
                'tipe' => 'required|in:masuk,keluar,adjustment',
                'referensi' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            // Find or create stock record
            $stock = ProdukStok::firstOrCreate(
                [
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $validated['pos_produk_id'],
                    'pos_toko_id' => $validated['pos_toko_id'],
                ],
                ['stok' => 0]
            );

            $stokSebelum = $stock->stok;
            
            // Calculate new stock based on type
            if ($validated['tipe'] === 'masuk') {
                $stokBaru = $stokSebelum + abs($validated['jumlah']);
            } else {
                $stokBaru = max(0, $stokSebelum - abs($validated['jumlah']));
            }

            $perubahan = $stokBaru - $stokSebelum;

            // Update stock
            $stock->update(['stok' => $stokBaru]);

            // Create log
            LogStok::create([
                'owner_id' => $ownerId,
                'pos_produk_id' => $validated['pos_produk_id'],
                'pos_toko_id' => $validated['pos_toko_id'],
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokBaru,
                'perubahan' => $perubahan,
                'tipe' => $validated['tipe'],
                'referensi' => $validated['referensi'] ?? 'Adjustment Stok',
                'keterangan' => $validated['keterangan'] ?? 'Adjustment stok melalui API',
                'pos_pengguna_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil disesuaikan',
                'data' => $stock->load(['produk.merk', 'toko']),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyesuaikan stok: ' . $e->getMessage(),
            ], 500);
        }
    }
}
