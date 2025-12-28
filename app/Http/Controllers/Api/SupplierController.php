<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
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
            
            $query = PosSupplier::where('owner_id', $ownerId)
                ->orderBy('created_at', 'desc');

            // Search by name
            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            // Search by phone
            if ($request->filled('nomor_hp')) {
                $query->where('nomor_hp', 'like', '%' . $request->nomor_hp . '%');
            }

            $suppliers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data supplier berhasil diambil',
                'data' => $suppliers->items(),
                'pagination' => [
                    'current_page' => $suppliers->currentPage(),
                    'per_page' => $suppliers->perPage(),
                    'total' => $suppliers->total(),
                    'last_page' => $suppliers->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nomor_hp' => 'nullable|string|max:45',
                'email' => 'nullable|email|max:255',
                'alamat' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string|max:255',
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

            $supplier = PosSupplier::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dibuat',
                'data' => $supplier,
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
                'message' => 'Gagal membuat supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified supplier
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

            $supplier = PosSupplier::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail supplier berhasil diambil',
                'data' => $supplier,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nomor_hp' => 'nullable|string|max:45',
                'email' => 'nullable|email|max:255',
                'alamat' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $supplier = PosSupplier::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan',
                ], 404);
            }

            $validated['slug'] = Str::slug($validated['nama'] . '-' . time());
            $supplier->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil diupdate',
                'data' => $supplier->fresh(),
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
                'message' => 'Gagal mengupdate supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified supplier
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

            $supplier = PosSupplier::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan',
                ], 404);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier: ' . $e->getMessage(),
            ], 500);
        }
    }
}
