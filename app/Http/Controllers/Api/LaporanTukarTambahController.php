<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTukarTambah;
use Illuminate\Http\Request;

class LaporanTukarTambahController extends Controller
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
            $query = PosTukarTambah::where('owner_id', $ownerId)
                ->with(['toko', 'pelanggan', 'produkMasuk', 'produkKeluar'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $tradeIns = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Laporan tukar tambah berhasil diambil',
                'data' => $tradeIns->items(),
                'pagination' => [
                    'current_page' => $tradeIns->currentPage(),
                    'per_page' => $tradeIns->perPage(),
                    'total' => $tradeIns->total(),
                    'last_page' => $tradeIns->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan tukar tambah: ' . $e->getMessage(),
            ], 500);
        }
    }
}
