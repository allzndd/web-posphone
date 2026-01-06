<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosToko;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of stores for mobile API
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

            $query = PosToko::where('owner_id', $ownerId)
                ->select('id', 'nama', 'alamat', 'created_at')
                ->orderBy('nama');

            // Search by store name or address
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('alamat', 'like', '%' . $search . '%');
                });
            }

            $stores = $query->get();

            return response()->json([
                'success' => true,
                'data' => $stores,
                'message' => 'Stores loaded successfully',
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
     * Display the specified store
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

            $store = PosToko::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $store,
                'message' => 'Store loaded successfully',
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
     * Create a new store
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

            $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
            ]);

            $store = PosToko::create([
                'owner_id' => $ownerId,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
            ]);

            return response()->json([
                'success' => true,
                'data' => $store,
                'message' => 'Store created successfully',
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
     * Update an existing store
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

            $store = PosToko::where('id', $id)
                ->where('owner_id', $ownerId)
                ->first();

            if (!$store) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Store not found or you do not have permission to update it',
                ], 404);
            }

            $request->validate([
                'nama' => 'required|string|max:255',
                'alamat' => 'required|string',
            ]);

            $store->update([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
            ]);

            return response()->json([
                'success' => true,
                'data' => $store,
                'message' => 'Store updated successfully',
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
     * Delete a store
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

            $store = PosToko::where('id', $id)
                ->where('owner_id', $ownerId)
                ->first();

            if (!$store) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Store not found or you do not have permission to delete it',
                ], 404);
            }

            $store->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Store deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}