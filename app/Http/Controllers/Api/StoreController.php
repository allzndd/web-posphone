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

            $stores = PosToko::where('owner_id', $ownerId)
                ->select('id', 'nama', 'alamat', 'created_at')
                ->orderBy('nama')
                ->get();

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
}