<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RingkasanKeuanganController extends Controller
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

            $period = $request->get('period', 'month');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            if ($period === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
            } elseif ($period === 'year') {
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
            } elseif ($period === 'week') {
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
            } else { // default month
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }

            $base = PosTransaksi::where('owner_id', $ownerId)
                ->whereBetween('created_at', [$start, $end]);

            $sales = (clone $base)->where('is_transaksi_masuk', 1);
            $purchases = (clone $base)->where('is_transaksi_masuk', 0);

            $totalRevenue = $sales->sum('total_harga');
            $totalExpenses = $purchases->sum('total_harga');
            $netProfit = $totalRevenue - $totalExpenses;

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan keuangan berhasil diambil',
                'data' => [
                    'period_start' => $start->toDateString(),
                    'period_end' => $end->toDateString(),
                    'total_revenue' => $totalRevenue,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan keuangan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
