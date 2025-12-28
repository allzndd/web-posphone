<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogStok;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockHistoryController extends Controller
{
    /**
     * Display stock history logs
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

            $perPage = $request->get('per_page', 10);
            
            $query = LogStok::where('owner_id', $ownerId)
                ->with(['produk.merk', 'toko', 'pengguna'])
                ->orderBy('created_at', 'desc');

            // Filter by product
            if ($request->filled('pos_produk_id')) {
                $query->where('pos_produk_id', $request->pos_produk_id);
            }

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }

            // Filter by type
            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            // Filter by product name
            if ($request->filled('nama_produk')) {
                $query->whereHas('produk', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama_produk . '%');
                });
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('created_at', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('created_at', '<=', $endDate);
            }

            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data riwayat stok berhasil diambil',
                'data' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data riwayat stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified stock history
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

            $log = LogStok::where('owner_id', $ownerId)
                ->with(['produk.merk', 'toko', 'pengguna'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail riwayat stok berhasil diambil',
                'data' => $log,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail riwayat stok: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get stock history summary
     */
    public function summary(Request $request)
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

            $query = LogStok::where('owner_id', $ownerId);

            // Filter by date range if provided
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('created_at', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('created_at', '<=', $endDate);
            }

            $totalMasuk = (clone $query)->where('tipe', 'masuk')->sum('perubahan');
            $totalKeluar = (clone $query)->where('tipe', 'keluar')->sum('perubahan');
            $totalAdjustment = (clone $query)->where('tipe', 'adjustment')->count();
            $totalLogs = $query->count();

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan riwayat stok berhasil diambil',
                'data' => [
                    'total_stock_in' => abs($totalMasuk),
                    'total_stock_out' => abs($totalKeluar),
                    'total_adjustments' => $totalAdjustment,
                    'total_logs' => $totalLogs,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan riwayat stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock history by product
     */
    public function byProduct(Request $request, $productId)
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

            $logs = LogStok::where('owner_id', $ownerId)
                ->where('pos_produk_id', $productId)
                ->with(['toko', 'pengguna'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat stok produk berhasil diambil',
                'data' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat stok produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}
