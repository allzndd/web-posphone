<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosKategoriExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of expense categories for mobile API
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $query = PosKategoriExpense::query();
            
            // If superadmin, only show global items
            if ($user->role_id === 1) {
                $query->where('is_global', 1);
            }
            // If owner, show global items and their own items
            elseif ($user->role_id === 2) {
                $query->where(function($q) use ($user) {
                    $q->where('is_global', 1)
                      ->orWhere('owner_id', $user->id);
                });
            }
            // If admin, show global items and owner's items
            elseif ($user->role_id === 3) {
                $ownerId = $user->owner ? $user->owner->id : null;
                $query->where(function($q) use ($ownerId) {
                    $q->where('is_global', 1)
                      ->orWhere('owner_id', $ownerId);
                });
            }

            // Search by category name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('nama', 'like', '%' . $search . '%');
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            $categories = $query->select('id', 'nama', 'owner_id', 'is_global', 'created_at', 'updated_at')
                ->orderBy('nama')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $categories->items(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
                'message' => 'Expense categories loaded successfully',
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
     * Display the specified expense category
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $query = PosKategoriExpense::where('id', $id);
            
            // If superadmin, only show global items
            if ($user->role_id === 1) {
                $query->where('is_global', 1);
            }
            // If owner, show global items and their own items
            elseif ($user->role_id === 2) {
                $query->where(function($q) use ($user) {
                    $q->where('is_global', 1)
                      ->orWhere('owner_id', $user->id);
                });
            }
            // If admin, show global items and owner's items
            elseif ($user->role_id === 3) {
                $ownerId = $user->owner ? $user->owner->id : null;
                $query->where(function($q) use ($ownerId) {
                    $q->where('is_global', 1)
                      ->orWhere('owner_id', $ownerId);
                });
            }

            $category = $query->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense category tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Expense category loaded successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new expense category
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = [
                'nama' => $request->nama,
            ];

            // For superadmin, set owner_id to null and is_global to 1
            if ($user->role_id === 1) {
                $data['owner_id'] = null;
                $data['is_global'] = 1;
            }
            // For owner, set owner_id to their ID and is_global to 0
            elseif ($user->role_id === 2) {
                $data['owner_id'] = $user->id;
                $data['is_global'] = 0;
            }
            // For admin, set is_global to 0 and owner_id to their owner's ID
            elseif ($user->role_id === 3) {
                $ownerId = $user->owner ? $user->owner->id : null;
                if (!$ownerId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Owner tidak ditemukan',
                    ], 403);
                }
                $data['owner_id'] = $ownerId;
                $data['is_global'] = 0;
            }

            $category = PosKategoriExpense::create($data);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Expense category created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing expense category
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();

            $query = PosKategoriExpense::where('id', $id);
            
            // Check authorization
            if ($user->role_id === 2) {
                // Owner can only update their own items
                $query->where('owner_id', $user->id);
            } elseif ($user->role_id === 3) {
                // Admin can only update items belonging to their owner
                $ownerId = $user->owner ? $user->owner->id : null;
                if (!$ownerId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Owner tidak ditemukan',
                    ], 403);
                }
                $query->where('owner_id', $ownerId);
            }

            $category = $query->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense category tidak ditemukan atau Anda tidak memiliki akses',
                ], 404);
            }

            // Prevent editing global items if not superadmin
            if ($category->is_global && $user->role_id !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengedit kategori global',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $category->update([
                'nama' => $request->nama,
            ]);

            return response()->json([
                'success' => true,
                'data' => $category->fresh(),
                'message' => 'Expense category updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an expense category
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            $query = PosKategoriExpense::where('id', $id);
            
            // Check authorization
            if ($user->role_id === 2) {
                // Owner can only delete their own items
                $query->where('owner_id', $user->id);
            } elseif ($user->role_id === 3) {
                // Admin can only delete items belonging to their owner
                $ownerId = $user->owner ? $user->owner->id : null;
                if (!$ownerId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Owner tidak ditemukan',
                    ], 403);
                }
                $query->where('owner_id', $ownerId);
            }

            $category = $query->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense category tidak ditemukan atau Anda tidak memiliki akses',
                ], 404);
            }

            // Prevent deleting global items if not superadmin
            if ($category->is_global && $user->role_id !== 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori global',
                ], 403);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense category deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
