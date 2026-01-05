<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosPelanggan;
use Illuminate\Http\Request;

class LaporanPelangganController extends Controller
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
            $sortBy = $request->get('sort_by', 'name');

            $query = PosPelanggan::where('owner_id', $ownerId)->with('transaksi');

            if ($sortBy === 'purchases') {
                $query->withCount('transaksi')->orderBy('transaksi_count', 'desc');
            } elseif ($sortBy === 'value') {
                $query->withSum('transaksi', 'total_harga')->orderBy('transaksi_sum_total_harga', 'desc');
            } else {
                $query->orderBy('nama');
            }

            $customers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Laporan pelanggan berhasil diambil',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
