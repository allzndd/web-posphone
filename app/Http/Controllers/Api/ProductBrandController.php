<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosProdukMerk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of product brands
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
            
            $query = PosProdukMerk::where('owner_id', $ownerId)
                ->withCount('produk')
                ->orderBy('created_at', 'desc');

            // Search by brand name
            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            $brands = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data merk produk berhasil diambil',
                'data' => $brands->items(),
                'pagination' => [
                    'current_page' => $brands->currentPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total(),
                    'last_page' => $brands->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data merk produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified brand
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

            $brand = PosProdukMerk::where('owner_id', $ownerId)
                ->withCount('produk')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail merk produk berhasil diambil',
                'data' => $brand,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail merk produk: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $validated['owner_id'] = $ownerId;
            $validated['slug'] = Str::slug($validated['nama'] . '-' . time());

            $brand = PosProdukMerk::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Merk produk berhasil dibuat',
                'data' => $brand,
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
                'message' => 'Gagal membuat merk produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $brand = PosProdukMerk::where('owner_id', $ownerId)->findOrFail($id);
            $brand->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Merk produk berhasil diupdate',
                'data' => $brand,
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
                'message' => 'Gagal mengupdate merk produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified brand
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

            $brand = PosProdukMerk::where('owner_id', $ownerId)
                ->withCount('produk')
                ->findOrFail($id);

            // Check if brand has products
            if ($brand->produk_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus merk karena masih memiliki produk terkait',
                ], 400);
            }

            $brand->delete();

            return response()->json([
                'success' => true,
                'message' => 'Merk produk berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus merk produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}
