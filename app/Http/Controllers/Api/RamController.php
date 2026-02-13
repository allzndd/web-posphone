<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosRam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RamController extends Controller
{
    /**
     * Display a listing of rams with pagination
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = PosRam::query();

            // Filter by global rams OR owner's rams
            $query->where(function($q) use ($user) {
                $q->where('is_global', 1)
                  ->orWhere('id_owner', $user->id);
            });

            // Search by capacity
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('kapasitas', 'like', '%' . $search . '%');
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $rams = $query->select('id', 'id_owner', 'pos_produk_id', 'kapasitas', 'is_global', 'created_at', 'updated_at')
                ->orderBy('kapasitas')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $rams->items(),
                'current_page' => $rams->currentPage(),
                'last_page' => $rams->lastPage(),
                'per_page' => $rams->perPage(),
                'total' => $rams->total(),
                'from' => $rams->firstItem(),
                'to' => $rams->lastItem(),
                'message' => 'RAMs loaded successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified ram
     */
    public function show(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $ram = PosRam::where('id', $id)
                ->where(function($q) use ($user) {
                    $q->where('is_global', 1)
                      ->orWhere('id_owner', $user->id);
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $ram,
                'message' => 'RAM loaded successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'RAM not found',
            ], 404);
        }
    }

    /**
     * Store a newly created ram
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'kapasitas' => 'required|string|max:50',
                'pos_produk_id' => 'sometimes|nullable|integer',
                'is_global' => 'sometimes|boolean',
            ]);

            $ram = PosRam::create([
                'id_owner' => $user->id,
                'pos_produk_id' => $validated['pos_produk_id'] ?? null,
                'kapasitas' => $validated['kapasitas'],
                'is_global' => $validated['is_global'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => $ram,
                'message' => 'RAM created successfully',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified ram
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $ram = PosRam::where('id', $id)
                ->where('id_owner', $user->id)
                ->firstOrFail();

            $validated = $request->validate([
                'kapasitas' => 'required|string|max:50',
                'pos_produk_id' => 'sometimes|nullable|integer',
                'is_global' => 'sometimes|boolean',
            ]);

            $ram->update([
                'kapasitas' => $validated['kapasitas'],
                'pos_produk_id' => $validated['pos_produk_id'] ?? $ram->pos_produk_id,
                'is_global' => $validated['is_global'] ?? $ram->is_global,
            ]);

            return response()->json([
                'success' => true,
                'data' => $ram,
                'message' => 'RAM updated successfully',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => 'Validation failed',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the specified ram
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $ram = PosRam::where('id', $id)
                ->where('id_owner', $user->id)
                ->firstOrFail();
            $ram->delete();

            return response()->json([
                'success' => true,
                'message' => 'RAM deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
