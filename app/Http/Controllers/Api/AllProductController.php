<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AllProductController extends Controller
{
    /**
     * Display a listing of all products
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
            
            $query = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko'])
                ->orderBy('created_at', 'desc');

            // Search by product name
            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            // Filter by brand
            if ($request->filled('pos_produk_merk_id')) {
                $query->where('pos_produk_merk_id', $request->pos_produk_merk_id);
            }

            // Filter by color
            if ($request->filled('warna')) {
                $query->where('warna', 'like', '%' . $request->warna . '%');
            }

            // Filter by storage
            if ($request->filled('penyimpanan')) {
                $query->where('penyimpanan', $request->penyimpanan);
            }

            // Sort by price
            if ($request->filled('sort_by')) {
                switch ($request->sort_by) {
                    case 'harga_jual_asc':
                        $query->orderBy('harga_jual', 'asc');
                        break;
                    case 'harga_jual_desc':
                        $query->orderBy('harga_jual', 'desc');
                        break;
                    case 'harga_beli_asc':
                        $query->orderBy('harga_beli', 'asc');
                        break;
                    case 'harga_beli_desc':
                        $query->orderBy('harga_beli', 'desc');
                        break;
                }
            }

            $products = $query->paginate($perPage);

            // Calculate total stock for each product
            $products->getCollection()->transform(function ($product) {
                $totalStock = $product->stok->sum('stok');
                $product->total_stok = $totalStock;
                return $product;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(Request $request, $id)
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

            $product = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko'])
                ->findOrFail($id);

            // Calculate total stock
            $product->total_stok = $product->stok->sum('stok');

            return response()->json([
                'success' => true,
                'message' => 'Detail produk berhasil diambil',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail produk: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
                'nama' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'warna' => 'nullable|string|max:100',
                'penyimpanan' => 'nullable|string|max:50',
                'battery_health' => 'nullable|string|max:10',
                'harga_beli' => 'required|numeric|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'biaya_tambahan' => 'nullable|array',
                'imei' => 'nullable|string|max:50',
                'aksesoris' => 'nullable|string',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            // Auto-generate nama if not provided
            $nama = $validated['nama'] ?? null;
            if (empty($nama)) {
                $merk = \App\Models\PosProdukMerk::find($validated['pos_produk_merk_id']);
                $namaParts = [];
                
                if ($merk && $merk->nama) {
                    $namaParts[] = $merk->nama;
                }
                
                if (!empty($validated['warna'])) {
                    $namaParts[] = $validated['warna'];
                }
                
                if (!empty($validated['penyimpanan'])) {
                    $namaParts[] = $validated['penyimpanan'] . 'GB';
                }
                
                $nama = !empty($namaParts) ? implode(' ', $namaParts) : 'Produk Baru';
            }
            
            $validated['nama'] = $nama;
            $validated['owner_id'] = $ownerId;
            $validated['slug'] = Str::slug($validated['nama'] . '-' . time());

            $product = PosProduk::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'data' => $product->load(['merk', 'stok.toko']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'pos_produk_merk_id' => 'sometimes|required|exists:pos_produk_merk,id',
                'nama' => 'sometimes|required|string|max:255',
                'deskripsi' => 'nullable|string',
                'warna' => 'nullable|string|max:100',
                'penyimpanan' => 'nullable|string|max:50',
                'battery_health' => 'nullable|string|max:10',
                'harga_beli' => 'sometimes|required|numeric|min:0',
                'harga_jual' => 'sometimes|required|numeric|min:0',
                'biaya_tambahan' => 'nullable|array',
                'imei' => 'nullable|string|max:50',
                'aksesoris' => 'nullable|string',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $product = PosProduk::where('owner_id', $ownerId)->findOrFail($id);
            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
                'data' => $product->load(['merk', 'stok.toko']),
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
                'message' => 'Gagal mengupdate produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Request $request, $id)
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

            $product = PosProduk::where('owner_id', $ownerId)->findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}
