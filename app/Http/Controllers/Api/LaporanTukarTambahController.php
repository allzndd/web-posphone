<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTukarTambah;
use App\Models\PosToko;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TradeInReportExport;

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
                ->with(['toko', 'pelanggan', 'produkMasuk', 'produkKeluar', 'transaksi'])
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

            // Search by customer name or product
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('pelanggan', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('produkMasuk', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('produkKeluar', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    });
                });
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
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data toko: ' . $e->getMessage(),
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

            $query = PosTukarTambah::where('owner_id', $ownerId)
                ->with(['toko', 'pelanggan', 'produkMasuk', 'produkKeluar', 'transaksi']);

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
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('pelanggan', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('produkMasuk', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('produkKeluar', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    });
                });
            }

            $tradeIns = $query->orderBy('created_at', 'desc')->get();

            // Calculate summary
            $totalTradeIns = $tradeIns->count();
            $totalTradeInValue = 0;
            $totalNewValue = 0;
            $totalAdditionalPayment = 0;

            foreach ($tradeIns as $tradeIn) {
                // Get trade in value from related transaction
                if ($tradeIn->transaksi && $tradeIn->transaksi->isNotEmpty()) {
                    $transaction = $tradeIn->transaksi->first();
                    $totalNewValue += floatval($transaction->total_harga ?? 0);
                }
                
                // Estimate trade in value (could be from product masuk price)
                if ($tradeIn->produkMasuk) {
                    $totalTradeInValue += floatval($tradeIn->produkMasuk->harga ?? 0);
                }
                
                // Calculate additional payment (difference between new and trade-in value)
                $newValue = 0;
                if ($tradeIn->transaksi && $tradeIn->transaksi->isNotEmpty()) {
                    $newValue = floatval($tradeIn->transaksi->first()->total_harga ?? 0);
                }
                $tradeInValue = $tradeIn->produkMasuk ? floatval($tradeIn->produkMasuk->harga ?? 0) : 0;
                $totalAdditionalPayment += ($newValue - $tradeInValue);
            }

            $averageTradeInValue = $totalTradeIns > 0 ? $totalTradeInValue / $totalTradeIns : 0;
            $totalValue = $totalTradeInValue + $totalAdditionalPayment;

            $summary = [
                'totalTradeIns' => $totalTradeIns,
                'totalTradeInValue' => $totalTradeInValue,
                'totalNewValue' => $totalNewValue,
                'totalAdditionalPayment' => $totalAdditionalPayment,
                'totalValue' => $totalValue,
                'averageTradeInValue' => $averageTradeInValue,
            ];

            $period = $request->get('period', 'all');

            return Excel::download(
                new TradeInReportExport($tradeIns, $summary, $period),
                'trade_in_report_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
