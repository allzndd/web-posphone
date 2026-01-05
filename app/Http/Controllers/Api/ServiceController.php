<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
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
            
            $query = PosService::where('owner_id', $ownerId)
                ->with(['toko'])
                ->orderBy('created_at', 'desc');

            // Search by service name
            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }

            $services = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data service berhasil diambil',
                'data' => $services->items(),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'last_page' => $services->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified service
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

            $service = PosService::where('owner_id', $ownerId)
                ->with(['toko'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail service berhasil diambil',
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail service: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
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

            $validated = $request->validate([
                'pos_toko_id' => 'required|exists:pos_toko,id',
                'nama' => 'required|string|max:45',
                'keterangan' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'durasi' => 'required|integer|min:1',
            ]);

            $service = PosService::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'nama' => $validated['nama'],
                'keterangan' => $validated['keterangan'] ?? null,
                'harga' => $validated['harga'],
                'durasi' => $validated['durasi'],
            ]);

            $service->load(['toko']);

            return response()->json([
                'success' => true,
                'message' => 'Service berhasil dibuat',
                'data' => $service,
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
                'message' => 'Gagal membuat service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
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

            $service = PosService::where('owner_id', $ownerId)->findOrFail($id);

            $validated = $request->validate([
                'pos_toko_id' => 'required|exists:pos_toko,id',
                'nama' => 'required|string|max:45',
                'keterangan' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'durasi' => 'required|integer|min:1',
            ]);

            $service->update([
                'pos_toko_id' => $validated['pos_toko_id'],
                'nama' => $validated['nama'],
                'keterangan' => $validated['keterangan'] ?? null,
                'harga' => $validated['harga'],
                'durasi' => $validated['durasi'],
            ]);

            $service->load(['toko']);

            return response()->json([
                'success' => true,
                'message' => 'Service berhasil diupdate',
                'data' => $service,
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
                'message' => 'Gagal mengupdate service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified service
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

            $service = PosService::where('owner_id', $ownerId)->findOrFail($id);
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus service: ' . $e->getMessage(),
            ], 500);
        }
    }
}
