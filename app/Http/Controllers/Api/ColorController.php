<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosWarna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColorController extends Controller
{
    /**
     * Display a listing of colors with pagination
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = PosWarna::query();

            // Filter by global colors OR owner's colors
            $query->where(function($q) use ($user) {
                $q->where('is_global', 1)
                  ->orWhere('id_owner', $user->id);
            });

            // Search by color name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('warna', 'like', '%' . $search . '%');
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $colors = $query->select('id', 'id_owner', 'warna', 'is_global', 'created_at', 'updated_at')
                ->orderBy('warna')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $colors->items(),
                'current_page' => $colors->currentPage(),
                'last_page' => $colors->lastPage(),
                'per_page' => $colors->perPage(),
                'total' => $colors->total(),
                'from' => $colors->firstItem(),
                'to' => $colors->lastItem(),
                'message' => 'Colors loaded successfully',
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
     * Display the specified color
     */
    public function show(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $color = PosWarna::where('id', $id)
                ->where(function($q) use ($user) {
                    $q->where('is_global', 1)
                      ->orWhere('id_owner', $user->id);
                })
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $color,
                'message' => 'Color loaded successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Color not found',
            ], 404);
        }
    }

    /**
     * Store a newly created color
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'warna' => 'required|string|max:100',
                'is_global' => 'sometimes|boolean',
            ]);

            $color = PosWarna::create([
                'id_owner' => $user->id,
                'warna' => $validated['warna'],
                'is_global' => $validated['is_global'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => $color,
                'message' => 'Color created successfully',
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
     * Update the specified color
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $color = PosWarna::where('id', $id)
                ->where('id_owner', $user->id)
                ->firstOrFail();

            $validated = $request->validate([
                'warna' => 'required|string|max:100',
                'is_global' => 'sometimes|boolean',
            ]);

            $color->update([
                'warna' => $validated['warna'],
                'is_global' => $validated['is_global'] ?? $color->is_global,
            ]);

            return response()->json([
                'success' => true,
                'data' => $color,
                'message' => 'Color updated successfully',
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
     * Delete the specified color
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $color = PosWarna::where('id', $id)
                ->where('id_owner', $user->id)
                ->firstOrFail();
            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'Color deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
