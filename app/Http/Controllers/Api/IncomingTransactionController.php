<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosProduk;
use App\Models\ProdukStok;
use App\Traits\UpdatesStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingTransactionController extends Controller
{
    use UpdatesStock;
    /**
     * Display a listing of incoming transactions (sales)
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
                    ->where('is_transaksi_masuk', 1)
                    ->with(['toko', 'pelanggan', 'tukarTambah', 'items.produk', 'items.service'])
                    ->orderBy('created_at', 'desc');

                // Filter by store
                if ($request->filled('pos_toko_id')) {
                    $query->where('pos_toko_id', $request->pos_toko_id);
                }

                // Filter by customer
                if ($request->filled('pos_pelanggan_id')) {
                    $query->where('pos_pelanggan_id', $request->pos_pelanggan_id);
                }

                // Filter by status
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                // Filter by payment method
                if ($request->filled('metode_pembayaran')) {
                    $query->where('metode_pembayaran', $request->metode_pembayaran);
                }

                // Search by invoice
                if ($request->filled('invoice')) {
                    $query->where('invoice', 'like', '%' . $request->invoice . '%');
                }

                // Filter by date range
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }

                $transactions = $query->paginate($perPage);

                return response()->json([
                    'success' => true,
                    'message' => 'Data transaksi masuk berhasil diambil',
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
                    'message' => 'Gagal mengambil data transaksi masuk: ' . $e->getMessage(),
                ], 500);
            }
        }

        /**
         * Display the specified incoming transaction
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
                    ->where('is_transaksi_masuk', 1)
                    ->with(['toko', 'pelanggan', 'tukarTambah', 'items.produk', 'items.service'])
                    ->findOrFail($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Detail transaksi masuk berhasil diambil',
                    'data' => $transaction,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil detail transaksi masuk: ' . $e->getMessage(),
                ], 404);
            }
        }

        /**
         * Store a newly created incoming transaction
         */
        public function store(Request $request)
        {
            DB::beginTransaction();
            try {
                $user = $request->user();
                $ownerId = $user->owner ? $user->owner->id : null;

                if (!$ownerId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Owner tidak ditemukan',
                    ], 403);
                }

                // Auto-generate invoice if not provided
                $invoice = $request->invoice;
                if (empty($invoice)) {
                    $invoice = $this->generateInvoiceNumber($ownerId, true);
                }

                $validated = $request->validate([
                    'pos_toko_id' => 'required|exists:pos_toko,id',
                    'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
                    'pos_tukar_tambah_id' => 'nullable|exists:pos_tukar_tambah,id',
                    'total_harga' => 'required|numeric|min:0',
                    'keterangan' => 'nullable|string',
                    'status' => 'required|in:pending,completed,cancelled',
                    'metode_pembayaran' => 'required|in:cash,debit,credit,transfer',
                    'items' => 'required|array|min:1',
                    'items.*.pos_produk_id' => 'nullable|exists:pos_produk,id',
                    'items.*.pos_service_id' => 'nullable|exists:pos_service,id',
                    'items.*.quantity' => 'required|integer|min:1',
                    'items.*.harga_satuan' => 'required|numeric|min:0',
                    'items.*.subtotal' => 'required|numeric|min:0',
                    'items.*.diskon' => 'nullable|numeric|min:0',
                    'items.*.garansi' => 'nullable|integer|min:0',
                    'items.*.garansi_expires_at' => 'nullable|date',
                    'items.*.pajak' => 'nullable|numeric|min:0',
                ]);

                // Validate stock for products before creating transaction
                foreach ($validated['items'] as $item) {
                    if (!empty($item['pos_produk_id'])) {
                        $quantity = $item['quantity'];
                        
                        // Get available stock for this product IN THE SELECTED STORE
                        $stokData = ProdukStok::where('owner_id', $ownerId)
                            ->where('pos_toko_id', $validated['pos_toko_id'])
                            ->where('pos_produk_id', $item['pos_produk_id'])
                            ->first();
                        
                        $availableStock = $stokData ? $stokData->stok : 0;
                        
                        if ($availableStock < $quantity) {
                            $produk = PosProduk::find($item['pos_produk_id']);
                            $productName = $produk ? $produk->nama : 'Product ID ' . $item['pos_produk_id'];
                            
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Insufficient stock for {$productName} in the selected store. Available: {$availableStock}, Requested: {$quantity}"
                            ], 422);
                        }
                    }
                }

                // Create transaction
                $transaction = PosTransaksi::create([
                    'owner_id' => $ownerId,
                    'pos_toko_id' => $validated['pos_toko_id'],
                    'pos_pelanggan_id' => $validated['pos_pelanggan_id'] ?? null,
                    'pos_tukar_tambah_id' => $validated['pos_tukar_tambah_id'] ?? null,
                    'is_transaksi_masuk' => 1,
                    'invoice' => $invoice,
                    'total_harga' => $validated['total_harga'],
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => $validated['status'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                ]);

                // Create transaction items and update stock
                foreach ($validated['items'] as $item) {
                    PosTransaksiItem::create([
                        'pos_transaksi_id' => $transaction->id,
                        'pos_produk_id' => $item['pos_produk_id'] ?? null,
                        'pos_service_id' => $item['pos_service_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $item['subtotal'],
                        'diskon' => $item['diskon'] ?? 0,
                        'garansi' => $item['garansi'] ?? null,
                        'garansi_expires_at' => $item['garansi_expires_at'] ?? null,
                        'pajak' => $item['pajak'] ?? 0,
                    ]);

                    // Update stock for products (not services)
                    // Incoming transaction (sales) = stock out (reduce stock)
                    if (!empty($item['pos_produk_id'])) {
                        $this->updateProductStock(
                            $ownerId,
                            $validated['pos_toko_id'],
                            $item['pos_produk_id'],
                            -$item['quantity'], // Negative for sales
                            'keluar',
                            $invoice,
                            'Penjualan produk via API'
                        );
                    }
                }

                $transaction->load(['toko', 'pelanggan', 'tukarTambah', 'items.produk', 'items.service']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi masuk berhasil dibuat',
                    'data' => $transaction,
                ], 201);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi masuk: ' . $e->getMessage(),
                ], 500);
            }
        }

        /**
         * Update the specified incoming transaction
         */
        public function update(Request $request, $id)
        {
            DB::beginTransaction();
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
                    ->where('is_transaksi_masuk', 1)
                    ->findOrFail($id);

                $validated = $request->validate([
                    'pos_toko_id' => 'required|exists:pos_toko,id',
                    'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
                    'pos_tukar_tambah_id' => 'nullable|exists:pos_tukar_tambah,id',
                    'total_harga' => 'required|numeric|min:0',
                    'keterangan' => 'nullable|string',
                    'status' => 'required|in:pending,completed,cancelled',
                    'metode_pembayaran' => 'required|in:cash,debit,credit,transfer',
                    'items' => 'required|array|min:1',
                    'items.*.pos_produk_id' => 'nullable|exists:pos_produk,id',
                    'items.*.pos_service_id' => 'nullable|exists:pos_service,id',
                    'items.*.quantity' => 'required|integer|min:1',
                    'items.*.harga_satuan' => 'required|numeric|min:0',
                    'items.*.subtotal' => 'required|numeric|min:0',
                    'items.*.diskon' => 'nullable|numeric|min:0',
                    'items.*.garansi' => 'nullable|integer|min:0',
                    'items.*.garansi_expires_at' => 'nullable|date',
                    'items.*.pajak' => 'nullable|numeric|min:0',
                ]);

                // Update transaction
                $transaction->update([
                    'pos_toko_id' => $validated['pos_toko_id'],
                    'pos_pelanggan_id' => $validated['pos_pelanggan_id'] ?? null,
                    'pos_tukar_tambah_id' => $validated['pos_tukar_tambah_id'] ?? null,
                    'total_harga' => $validated['total_harga'],
                    'keterangan' => $validated['keterangan'] ?? null,
                    'status' => $validated['status'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                ]);

                // Delete old items and create new ones
                $transaction->items()->delete();
                foreach ($validated['items'] as $item) {
                    PosTransaksiItem::create([
                        'pos_transaksi_id' => $transaction->id,
                        'pos_produk_id' => $item['pos_produk_id'] ?? null,
                        'pos_service_id' => $item['pos_service_id'] ?? null,
                        'quantity' => $item['quantity'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $item['subtotal'],
                        'diskon' => $item['diskon'] ?? 0,
                        'garansi' => $item['garansi'] ?? null,
                        'garansi_expires_at' => $item['garansi_expires_at'] ?? null,
                        'pajak' => $item['pajak'] ?? 0,
                    ]);
                }

                $transaction->load(['toko', 'pelanggan', 'tukarTambah', 'items.produk', 'items.service']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi masuk berhasil diupdate',
                    'data' => $transaction,
                ], 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors(),
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengupdate transaksi masuk: ' . $e->getMessage(),
                ], 500);
            }
        }

        /**
         * Remove the specified incoming transaction
         */
        public function destroy(Request $request, $id)
        {
            DB::beginTransaction();
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
                    ->where('is_transaksi_masuk', 1)
                    ->findOrFail($id);

                // Delete items first
                $transaction->items()->delete();
                
                // Delete transaction
                $transaction->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi masuk berhasil dihapus',
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus transaksi masuk: ' . $e->getMessage(),
                ], 500);
            }
        }

        /**
         * Get transaction summary statistics
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

                $query = PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', 1);

                // Filter by date range
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }

                $totalTransactions = $query->count();
                $totalRevenue = $query->sum('total_harga');
                $pendingCount = (clone $query)->where('status', 'pending')->count();
                $completedCount = (clone $query)->where('status', 'completed')->count();
                $cancelledCount = (clone $query)->where('status', 'cancelled')->count();

                return response()->json([
                    'success' => true,
                    'message' => 'Summary transaksi masuk berhasil diambil',
                    'data' => [
                        'total_transactions' => $totalTransactions,
                        'total_revenue' => $totalRevenue,
                        'pending_count' => $pendingCount,
                        'completed_count' => $completedCount,
                        'cancelled_count' => $cancelledCount,
                    ],
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil summary transaksi masuk: ' . $e->getMessage(),
                ], 500);
            }
        }

        /**
         * Generate unique invoice number with retry mechanism
         */
        private function generateInvoiceNumber($ownerId, $isMasuk = true)
        {
            $maxRetries = 10;
            $attempt = 0;

            while ($attempt < $maxRetries) {
                $prefix = $isMasuk ? 'INV-IN-' : 'INV-OUT-';
                $date = now()->format('Ymd');
                
                // Get last invoice number for today
                $lastInvoice = PosTransaksi::where('owner_id', $ownerId)
                    ->where('is_transaksi_masuk', $isMasuk ? 1 : 0)
                    ->where('invoice', 'like', $prefix . $date . '%')
                    ->orderBy('invoice', 'desc')
                    ->first();

                if ($lastInvoice) {
                    // Extract number from last invoice
                    $lastNumber = (int) substr($lastInvoice->invoice, -4);
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $invoice = $prefix . $date . '-' . $newNumber;

                // Check if invoice is unique
                $exists = PosTransaksi::where('invoice', $invoice)->exists();
                if (!$exists) {
                    return $invoice;
                }

                $attempt++;
            }

            // Fallback: use timestamp if all retries failed
            return $prefix . $date . '-' . now()->timestamp;
        }
    }
