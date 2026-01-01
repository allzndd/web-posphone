<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use App\Models\PosToko;
use App\Models\PosTransaksi;
use Illuminate\Http\Request;

class SemuaLaporanController extends Controller
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

            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $transactions = PosTransaksi::where('owner_id', $ownerId);
            if ($startDate) {
                $transactions->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $transactions->whereDate('created_at', '<=', $endDate);
            }

            $totalTransactions = (clone $transactions)->count();
            $totalRevenue = (clone $transactions)->where('is_transaksi_masuk', 1)->sum('total_harga');
            $totalExpenses = (clone $transactions)->where('is_transaksi_masuk', 0)->sum('total_harga');

            return response()->json([
                'success' => true,
                'message' => 'Summary laporan berhasil diambil',
                'data' => [
                    'total_transactions' => $totalTransactions,
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'total_customers' => PosPelanggan::where('owner_id', $ownerId)->count(),
                    'total_products' => PosProduk::where('owner_id', $ownerId)->count(),
                    'total_stores' => PosToko::where('owner_id', $ownerId)->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary laporan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
