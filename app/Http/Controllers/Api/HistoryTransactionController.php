<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use Illuminate\Http\Request;

class HistoryTransactionController extends Controller
{
    /**
     * Display a listing of all transaction history (incoming and outgoing).
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

            $query = PosTransaksi::where('owner_id', $ownerId)
                ->with(['toko', 'pelanggan', 'supplier', 'tukarTambah', 'items.produk', 'items.service'])
                ->orderBy('created_at', 'desc');

            $this->applyFilters($query, $request);

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data history transaksi berhasil diambil',
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
                'message' => 'Gagal mengambil data history transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified transaction from history.
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

            $transaction = PosTransaksi::where('owner_id', $ownerId)
                ->with(['toko', 'pelanggan', 'supplier', 'tukarTambah', 'items.produk', 'items.service'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail history transaksi berhasil diambil',
                'data' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail history transaksi: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get summary statistics for transaction history.
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

            $baseQuery = PosTransaksi::where('owner_id', $ownerId);
            $this->applyFilters($baseQuery, $request);

            $totalTransactions = (clone $baseQuery)->count();
            $incomingQuery = (clone $baseQuery)->where('is_transaksi_masuk', 1);
            $outgoingQuery = (clone $baseQuery)->where('is_transaksi_masuk', 0);

            $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
            $completedCount = (clone $baseQuery)->where('status', 'completed')->count();
            $cancelledCount = (clone $baseQuery)->where('status', 'cancelled')->count();

            return response()->json([
                'success' => true,
                'message' => 'Summary history transaksi berhasil diambil',
                'data' => [
                    'total_transactions' => $totalTransactions,
                    'incoming_transactions' => $incomingQuery->count(),
                    'outgoing_transactions' => $outgoingQuery->count(),
                    'total_revenue' => $incomingQuery->sum('total_harga'),
                    'total_expenses' => $outgoingQuery->sum('total_harga'),
                    'pending_count' => $pendingCount,
                    'completed_count' => $completedCount,
                    'cancelled_count' => $cancelledCount,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary history transaksi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Apply common filters for transaction history.
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by type string (incoming/outgoing) for convenience
        if ($request->filled('type')) {
            $type = strtolower($request->type);
            if (in_array($type, ['incoming', 'masuk'], true)) {
                $query->where('is_transaksi_masuk', 1);
            } elseif (in_array($type, ['outgoing', 'keluar'], true)) {
                $query->where('is_transaksi_masuk', 0);
            }
        }

        // Filter by explicit flag if provided
        if ($request->filled('is_transaksi_masuk')) {
            $query->where('is_transaksi_masuk', $request->is_transaksi_masuk);
        }

        if ($request->filled('pos_toko_id')) {
            $query->where('pos_toko_id', $request->pos_toko_id);
        }

        if ($request->filled('pos_pelanggan_id')) {
            $query->where('pos_pelanggan_id', $request->pos_pelanggan_id);
        }

        if ($request->filled('pos_supplier_id')) {
            $query->where('pos_supplier_id', $request->pos_supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('invoice')) {
            $query->where('invoice', 'like', '%' . $request->invoice . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    }
}
