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
            
            // Include global brands (is_global = 1) and owner's brands
            $query = PosProdukMerk::where(function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId)
                  ->orWhere('is_global', 1);
            })
                ->withCount('produk')
                ->orderBy('created_at', 'desc');

            // Search by brand name
            if ($request->filled('merk')) {
                $query->where('merk', 'like', '%' . $request->merk . '%');
            }

            // Support old parameter for backward compatibility
            if ($request->filled('nama')) {
                $query->where('merk', 'like', '%' . $request->nama . '%');
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
                'merk' => 'required|string|max:255',
                'nama' => 'nullable|string|max:255', // Keep nama for compatibility
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            // Use merk as the primary field
            $merkValue = $validated['merk'];
            $validated['owner_id'] = $ownerId;
            $validated['merk'] = $merkValue;
            $validated['nama'] = $validated['nama'] ?? $merkValue; // Use merk as nama if not provided
            $validated['slug'] = Str::slug($merkValue . '-' . time());

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
                'merk' => 'required|string|max:255',
                'nama' => 'nullable|string|max:255', // Keep nama for compatibility
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
            
            // Update merk and nama
            $merkValue = $validated['merk'];
            $updateData = [
                'merk' => $merkValue,
                'nama' => $validated['nama'] ?? $merkValue, // Use merk as nama if not provided
            ];
            $brand->update($updateData);

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
