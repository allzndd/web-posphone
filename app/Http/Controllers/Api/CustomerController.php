<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
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

            $perPage = $request->get('per_page', 20);
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'nama');
            $sortOrder = $request->get('sort_order', 'asc');

            $query = PosPelanggan::where('owner_id', $ownerId)
                ->withCount('transaksi')
                ->withSum('transaksi', 'total_harga');

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('nomor_hp', 'like', "%{$search}%");
                });
            }

            // Sorting
            if ($sortBy === 'transactions') {
                $query->orderBy('transaksi_count', $sortOrder);
            } elseif ($sortBy === 'value') {
                $query->orderBy('transaksi_sum_total_harga', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            $customers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil diambil',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new customer
     */
    public function store(Request $request)
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

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email|unique:pos_pelanggan,email',
                'nomor_hp' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'tanggal_bergabung' => 'nullable|date',
            ]);

            $validated['owner_id'] = $ownerId;
            $validated['slug'] = Str::slug($validated['nama']);
            $validated['tanggal_bergabung'] = $validated['tanggal_bergabung'] ?? now()->format('Y-m-d');

            $customer = PosPelanggan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan',
                'data' => $customer,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific customer
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

            $customer = PosPelanggan::where('owner_id', $ownerId)
                ->where('id', $id)
                ->withCount('transaksi')
                ->withSum('transaksi', 'total_harga')
                ->with(['transaksi' => function ($query) {
                    $query->orderBy('created_at', 'desc')->take(5);
                }])
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pelanggan berhasil diambil',
                'data' => $customer,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a customer
     */
    public function update(Request $request, $id)
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

            $customer = PosPelanggan::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email|unique:pos_pelanggan,email,' . $customer->id,
                'nomor_hp' => 'nullable|string|max:20',
                'alamat' => 'nullable|string',
                'tanggal_bergabung' => 'nullable|date',
            ]);

            $validated['slug'] = Str::slug($validated['nama']);

            $customer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui',
                'data' => $customer,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a customer
     */
    public function destroy(Request $request, $id)
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

            $customer = PosPelanggan::where('owner_id', $ownerId)
                ->where('id', $id)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan',
                ], 404);
            }

            // Check if customer has transactions
            if ($customer->transaksi()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus pelanggan yang memiliki riwayat transaksi',
                ], 400);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer statistics
     */
    public function stats(Request $request)
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

            $totalCustomers = PosPelanggan::where('owner_id', $ownerId)->count();
            
            $newThisMonth = PosPelanggan::where('owner_id', $ownerId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $avgTransactionValue = PosPelanggan::where('owner_id', $ownerId)
                ->withSum('transaksi', 'total_harga')
                ->get()
                ->filter(function ($customer) {
                    return $customer->transaksi_sum_total_harga > 0;
                })
                ->avg('transaksi_sum_total_harga') ?? 0;

            $totalTransactions = \App\Models\PosTransaksi::whereHas('pelanggan', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })->count();

            return response()->json([
                'success' => true,
                'message' => 'Statistik pelanggan berhasil diambil',
                'data' => [
                    'total_customers' => $totalCustomers,
                    'new_this_month' => $newThisMonth,
                    'avg_transaction_value' => round($avgTransactionValue, 2),
                    'total_transactions' => $totalTransactions,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik pelanggan: ' . $e->getMessage(),
            ], 500);
        }
    }
}