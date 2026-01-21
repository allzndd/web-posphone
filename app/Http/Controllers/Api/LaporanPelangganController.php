<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosPelanggan;
use App\Exports\CustomersReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'name');

            $query = PosPelanggan::where('owner_id', $ownerId)
                ->with(['transaksi' => function($q) {
                    $q->select('id', 'pos_pelanggan_id', 'total_harga', 'created_at');
                }]);

            // Search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_hp', 'LIKE', "%{$search}%")
                      ->orWhere('alamat', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            if ($sortBy === 'purchases') {
                $query->withCount('transaksi')->orderBy('transaksi_count', 'desc');
            } elseif ($sortBy === 'value') {
                $query->withSum('transaksi', 'total_harga')->orderBy('transaksi_sum_total_harga', 'desc');
            } elseif ($sortBy === 'recent') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('nama');
            }

            $customers = $query->paginate($perPage);

            // Transform data
            $transformedData = $customers->getCollection()->map(function($customer) {
                $totalPurchases = $customer->transaksi->count();
                $totalValue = $customer->transaksi->sum('total_harga');
                $lastPurchase = $customer->transaksi->sortByDesc('created_at')->first();

                return [
                    'id' => $customer->id,
                    'name' => $customer->nama ?? '-',
                    'email' => $customer->email ?? '-',
                    'phone' => $customer->nomor_hp ?? '-',
                    'address' => $customer->alamat ?? '-',
                    'total_purchases' => $totalPurchases,
                    'total_value' => $totalValue,
                    'average_purchase' => $totalPurchases > 0 ? $totalValue / $totalPurchases : 0,
                    'last_purchase_date' => $lastPurchase ? $lastPurchase->created_at->format('Y-m-d') : null,
                    'status' => $this->getCustomerStatus($totalPurchases, $lastPurchase),
                    'created_at' => $customer->created_at->format('Y-m-d'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Laporan pelanggan berhasil diambil',
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in LaporanPelangganController: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan pelanggan: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSummary(Request $request)
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

            $query = PosPelanggan::where('owner_id', $ownerId)->with('transaksi');

            $customers = $query->get();

            $totalCustomers = $customers->count();
            $totalPurchases = $customers->sum(function($c) {
                return $c->transaksi->count();
            });
            $totalValue = $customers->sum(function($c) {
                return $c->transaksi->sum('total_harga');
            });
            $activeCustomers = $customers->filter(function($c) {
                $lastPurchase = $c->transaksi->sortByDesc('created_at')->first();
                if (!$lastPurchase) return false;
                return $lastPurchase->created_at->diffInDays(now()) <= 30;
            })->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'total_purchases' => $totalPurchases,
                    'total_value' => $totalValue,
                    'active_customers' => $activeCustomers,
                    'average_value' => $totalCustomers > 0 ? $totalValue / $totalCustomers : 0,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary: ' . $e->getMessage(),
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

            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'name');

            $query = PosPelanggan::where('owner_id', $ownerId)->with('transaksi');

            // Search
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_hp', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            if ($sortBy === 'purchases') {
                $query->withCount('transaksi')->orderBy('transaksi_count', 'desc');
            } elseif ($sortBy === 'value') {
                $query->withSum('transaksi', 'total_harga')->orderBy('transaksi_sum_total_harga', 'desc');
            } else {
                $query->orderBy('nama');
            }

            $customers = $query->get();

            $summary = [
                'totalCustomers' => $customers->count(),
                'totalPurchases' => $customers->sum(function($c) { return $c->transaksi->count(); }),
                'totalValue' => $customers->sum(function($c) { return $c->transaksi->sum('total_harga'); }),
                'averageValue' => $customers->count() > 0 ? $customers->sum(function($c) { return $c->transaksi->sum('total_harga'); }) / $customers->count() : 0,
            ];

            return Excel::download(
                new CustomersReportExport($customers, $sortBy, $summary),
                'Laporan_Pelanggan_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Error in exportExcel: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getCustomerStatus($totalPurchases, $lastPurchase)
    {
        if ($totalPurchases == 0) {
            return 'new';
        }

        if ($lastPurchase) {
            $daysSinceLastPurchase = $lastPurchase->created_at->diffInDays(now());
            
            if ($daysSinceLastPurchase <= 30) {
                return 'active';
            } elseif ($daysSinceLastPurchase <= 90) {
                return 'inactive';
            }
        }

        return 'dormant';
    }
}
