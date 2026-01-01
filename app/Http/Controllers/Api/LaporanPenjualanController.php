<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use Illuminate\Http\Request;

class LaporanPenjualanController extends Controller
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
            $query = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->with(['toko', 'pelanggan', 'items.produk'])
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

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Laporan penjualan berhasil diambil',
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'last_page' => $transactions->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan penjualan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
