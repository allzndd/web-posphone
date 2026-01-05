<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProdukStok;
use Illuminate\Http\Request;

class LaporanStokController extends Controller
{
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
            $query = ProdukStok::with(['produk', 'toko'])
                ->where('owner_id', $ownerId)
                ->orderBy('stok', 'asc');

            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }
            if ($request->boolean('low_stock')) {
                $query->where('stok', '<=', 5);
            }

            $stocks = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Laporan stok berhasil diambil',
                'data' => $stocks->items(),
                'pagination' => [
                    'current_page' => $stocks->currentPage(),
                    'per_page' => $stocks->perPage(),
                    'total' => $stocks->total(),
                    'last_page' => $stocks->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan stok: ' . $e->getMessage(),
            ], 500);
        }
    }
}
