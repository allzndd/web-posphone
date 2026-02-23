<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use App\Models\PosToko;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

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
                ->where('status', 'completed')
                ->with(['toko', 'pelanggan', 'items.produk'])
                ->orderBy('created_at', 'desc');

            // Filter by store
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Filter by payment method
            if ($request->filled('payment_method')) {
                $query->where('metode_pembayaran', $request->payment_method);
            }

            // Search by invoice or customer name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'LIKE', "%{$search}%")
                      ->orWhereHas('pelanggan', function ($q) use ($search) {
                          $q->where('nama', 'LIKE', "%{$search}%");
                      });
                });
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

    public function getStores(Request $request)
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
                ->select('id', 'nama')
                ->orderBy('nama', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar toko berhasil diambil',
                'data' => $stores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar toko: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
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

            $query = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 1)
                ->where('status', 'completed')
                ->with(['toko', 'pelanggan', 'items.produk'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('pos_toko_id')) {
                $query->where('pos_toko_id', $request->pos_toko_id);
            }
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('payment_method')) {
                $query->where('metode_pembayaran', $request->payment_method);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'LIKE', "%{$search}%")
                      ->orWhereHas('pelanggan', function ($q) use ($search) {
                          $q->where('nama', 'LIKE', "%{$search}%");
                      });
                });
            }

            $transactions = $query->get();

            // Calculate summary
            $totalSales = $transactions->sum('total_harga');
            $totalTransactions = $transactions->count();
            $totalItems = 0;
            foreach ($transactions as $trx) {
                $totalItems += $trx->items->sum('quantity');
            }
            $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

            $summary = [
                'totalTransactions' => $totalTransactions,
                'totalSales' => $totalSales,
                'totalItems' => $totalItems,
                'averageTransaction' => $averageTransaction,
            ];

            // Determine period
            $period = $request->get('period', 'all');

            $fileName = 'sales_report_' . date('Y-m-d_His') . '.xlsx';

            return Excel::download(new SalesReportExport($transactions, $summary, $period), $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
