<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTransaksi;
use App\Models\PosKategoriExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseTransactionController extends Controller
{
    /**
     * Generate unique expense invoice number
     * 
     * @param int $ownerId
     * @return string
     */
    private function generateExpenseInvoice($ownerId)
    {
        $maxRetries = 10;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            $prefix = 'EXP-';
            
            // Get last expense transaction
            $lastExpense = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->where('invoice', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();
            
            $dateStr = date('Ymd');
            $nextNumber = 1;
            
            if ($lastExpense && $lastExpense->invoice) {
                // Parse last invoice: EXP-YYYYMMDD-XXXX
                $parts = explode('-', $lastExpense->invoice);
                if (count($parts) === 3) {
                    $lastDate = $parts[1];
                    $lastNumber = intval($parts[2]);
                    
                    // If same date, increment. Otherwise start from 1
                    if ($lastDate === $dateStr) {
                        $nextNumber = $lastNumber + 1;
                    }
                }
            }
            
            $invoiceNumber = $prefix . $dateStr . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // Check if invoice exists
            $exists = PosTransaksi::where('owner_id', $ownerId)
                ->where('invoice', $invoiceNumber)
                ->exists();
            
            if (!$exists) {
                return $invoiceNumber;
            }
            
            $attempt++;
            usleep(100000); // 0.1 second
        }
        
        // Fallback: use timestamp if all retries failed
        return $prefix . date('Ymd-His') . '-' . rand(1000, 9999);
    }
    /**
     * Display a listing of expense transactions.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get owner_id based on user role
            $ownerId = $user->role_id === 3 ? ($user->owner ? $user->owner->id : null) : $user->id;
            
            // Debug log
            \Log::info('[EXPENSE INDEX] User ID: ' . $user->id . ', Role: ' . $user->role_id . ', Owner ID: ' . $ownerId);
            \Log::info('[EXPENSE INDEX] Request params: ', $request->all());
            
            // Check total data in pos_transaksi for debugging
            $totalInTable = PosTransaksi::count();
            $totalExpense = PosTransaksi::where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->count();
            $totalForOwner = PosTransaksi::where('owner_id', $ownerId)->count();
            
            \Log::info('[EXPENSE INDEX DEBUG] Total in pos_transaksi: ' . $totalInTable);
            \Log::info('[EXPENSE INDEX DEBUG] Total expense transactions: ' . $totalExpense);
            \Log::info('[EXPENSE INDEX DEBUG] Total for owner_id ' . $ownerId . ': ' . $totalForOwner);
            
            // Get sample data to see what owner_ids exist
            $sampleExpenses = PosTransaksi::where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->select('id', 'owner_id', 'invoice', 'pos_kategori_expense_id')
                ->limit(5)
                ->get();
            \Log::info('[EXPENSE INDEX DEBUG] Sample expense data: ', $sampleExpenses->toArray());
            
            // Filter for expense transactions only
            $query = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->orderBy('created_at', 'desc');
            
            // Debug: Count total matching records
            $totalMatching = $query->count();
            \Log::info('[EXPENSE INDEX] Total matching records: ' . $totalMatching);
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('invoice', 'like', "%{$searchTerm}%")
                      ->orWhere('keterangan', 'like', "%{$searchTerm}%")
                      ->orWhere('metode_pembayaran', 'like', "%{$searchTerm}%");
                });
            }
            
            $perPage = $request->get('per_page', 10);
            $transactions = $query->paginate($perPage);
            
            // Debug: Log actual results
            \Log::info('[EXPENSE INDEX] Fetched ' . $transactions->count() . ' transactions out of ' . $transactions->total());
            if ($transactions->count() > 0) {
                \Log::info('[EXPENSE INDEX] First transaction: ID=' . $transactions->first()->id . ', Invoice=' . $transactions->first()->invoice);
            }
            
            // Calculate statistics for expense transactions only
            $totalTransactions = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->count();
            $totalExpense = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->sum('total_harga');
            
            // Preload categories and stores to avoid N+1 queries
            $categoryIds = $transactions->pluck('pos_kategori_expense_id')->filter()->unique()->toArray();
            $storeIds = $transactions->pluck('pos_toko_id')->filter()->unique()->toArray();
            
            $categories = PosKategoriExpense::whereIn('id', $categoryIds)->get()->keyBy('id');
            $stores = \App\Models\PosToko::whereIn('id', $storeIds)->get()->keyBy('id');
            
            $data = $transactions->map(function ($transaction) use ($categories, $stores) {
                // Get category and store names from preloaded data
                $categoryName = isset($categories[$transaction->pos_kategori_expense_id]) 
                    ? $categories[$transaction->pos_kategori_expense_id]->nama 
                    : null;
                    
                $storeName = isset($stores[$transaction->pos_toko_id]) 
                    ? $stores[$transaction->pos_toko_id]->nama 
                    : null;
                
                return [
                    'id' => $transaction->id,
                    'owner_id' => $transaction->owner_id,
                    'pos_toko_id' => $transaction->pos_toko_id,
                    'pos_kategori_expense_id' => $transaction->pos_kategori_expense_id,
                    'is_transaksi_masuk' => $transaction->is_transaksi_masuk,
                    'invoice' => $transaction->invoice,
                    'total_harga' => $transaction->total_harga,
                    'keterangan' => $transaction->keterangan,
                    'metode_pembayaran' => $transaction->metode_pembayaran,
                    'status' => $transaction->status,
                    'payment_status' => $transaction->payment_status,
                    'paid_amount' => $transaction->paid_amount,
                    'due_date' => $transaction->due_date,
                    'payment_terms' => $transaction->payment_terms,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'kategori_expense_name' => $categoryName,
                    'toko_name' => $storeName,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Transactions retrieved successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
                'statistics' => [
                    'total_transactions' => $totalTransactions,
                    'total_expense' => $totalExpense,
                ],
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created expense transaction.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pos_kategori_expense_id' => 'required|exists:pos_kategori_expense,id',
                'total_harga' => 'required|numeric|min:0',
                'pos_toko_id' => 'nullable|exists:pos_toko,id',
                'keterangan' => 'nullable|string|max:1000',
                'metode_pembayaran' => 'nullable|string|max:50',
                'invoice' => 'nullable|string|max:100|unique:pos_transaksi,invoice',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            
            // Get owner_id based on user role
            $ownerId = $user->role_id === 3 ? ($user->owner ? $user->owner->id : null) : $user->id;
            
            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found',
                ], 403);
            }
            
            // Generate invoice number if not provided
            $invoiceNumber = $request->invoice;
            if (!$invoiceNumber) {
                $invoiceNumber = $this->generateExpenseInvoice($ownerId);
            }

            // Default status to pending if not provided
            $status = $request->status ?? 'pending';

            $transaction = PosTransaksi::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $request->pos_toko_id,
                'pos_kategori_expense_id' => $request->pos_kategori_expense_id,
                'is_transaksi_masuk' => 0, // Mark as expense transaction
                'invoice' => $invoiceNumber,
                'total_harga' => $request->total_harga,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $status,
                'payment_status' => $status === 'completed' ? 'paid' : 'unpaid',
                'paid_amount' => $status === 'completed' ? $request->total_harga : 0,
            ]);
            
            // Debug log
            \Log::info('[EXPENSE CREATE] Transaction created: ID=' . $transaction->id . ', Owner=' . $transaction->owner_id . ', is_transaksi_masuk=' . $transaction->is_transaksi_masuk . ', kategori_expense_id=' . $transaction->pos_kategori_expense_id);

            // Get category and store names
            $categoryName = null;
            $storeName = null;
            
            if ($transaction->pos_kategori_expense_id) {
                $category = PosKategoriExpense::find($transaction->pos_kategori_expense_id);
                $categoryName = $category ? $category->nama : null;
            }
            
            if ($transaction->pos_toko_id) {
                $store = \App\Models\PosToko::find($transaction->pos_toko_id);
                $storeName = $store ? $store->nama : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => [
                    'id' => $transaction->id,
                    'owner_id' => $transaction->owner_id,
                    'pos_toko_id' => $transaction->pos_toko_id,
                    'pos_kategori_expense_id' => $transaction->pos_kategori_expense_id,
                    'is_transaksi_masuk' => $transaction->is_transaksi_masuk,
                    'invoice' => $transaction->invoice,
                    'total_harga' => $transaction->total_harga,
                    'keterangan' => $transaction->keterangan,
                    'metode_pembayaran' => $transaction->metode_pembayaran,
                    'status' => $transaction->status,
                    'payment_status' => $transaction->payment_status,
                    'paid_amount' => $transaction->paid_amount,
                    'due_date' => $transaction->due_date,
                    'payment_terms' => $transaction->payment_terms,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'kategori_expense_name' => $categoryName,
                    'toko_name' => $storeName,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified expense transaction.
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            // Get owner_id based on user role
            $ownerId = $user->role_id === 3 ? ($user->owner ? $user->owner->id : null) : $user->id;
            
            $transaction = PosTransaksi::where('owner_id', $ownerId)
                ->where('id', $id)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }
            
            // Get category and store names manually
            $categoryName = null;
            $storeName = null;
            
            if ($transaction->pos_kategori_expense_id) {
                $category = PosKategoriExpense::find($transaction->pos_kategori_expense_id);
                $categoryName = $category ? $category->nama : null;
            }
            
            if ($transaction->pos_toko_id) {
                $store = \App\Models\PosToko::find($transaction->pos_toko_id);
                $storeName = $store ? $store->nama : null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction retrieved successfully',
                'data' => [
                    'id' => $transaction->id,
                    'owner_id' => $transaction->owner_id,
                    'pos_toko_id' => $transaction->pos_toko_id,
                    'pos_kategori_expense_id' => $transaction->pos_kategori_expense_id,
                    'is_transaksi_masuk' => $transaction->is_transaksi_masuk,
                    'invoice' => $transaction->invoice,
                    'total_harga' => $transaction->total_harga,
                    'keterangan' => $transaction->keterangan,
                    'metode_pembayaran' => $transaction->metode_pembayaran,
                    'status' => $transaction->status,
                    'payment_status' => $transaction->payment_status,
                    'paid_amount' => $transaction->paid_amount,
                    'due_date' => $transaction->due_date,
                    'payment_terms' => $transaction->payment_terms,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'kategori_expense_name' => $categoryName,
                    'toko_name' => $storeName,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified expense transaction.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pos_kategori_expense_id' => 'required|exists:pos_kategori_expense,id',
                'total_harga' => 'required|numeric|min:0',
                'pos_toko_id' => 'nullable|exists:pos_toko,id',
                'keterangan' => 'nullable|string|max:1000',
                'metode_pembayaran' => 'nullable|string|max:50',
                'invoice' => 'nullable|string|max:100|unique:pos_transaksi,invoice,' . $id,
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            
            // Get owner_id based on user role
            $ownerId = $user->role_id === 3 ? ($user->owner ? $user->owner->id : null) : $user->id;
            
            $transaction = PosTransaksi::where('owner_id', $ownerId)
                ->where('id', $id)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            $updateData = [
                'pos_toko_id' => $request->pos_toko_id,
                'pos_kategori_expense_id' => $request->pos_kategori_expense_id,
                'total_harga' => $request->total_harga,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
            ];

            // Update invoice if provided
            if ($request->has('invoice')) {
                $updateData['invoice'] = $request->invoice;
            }

            // Update status if provided
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
                $updateData['payment_status'] = $request->status === 'completed' ? 'paid' : 'unpaid';
                $updateData['paid_amount'] = $request->status === 'completed' ? $request->total_harga : 0;
            } else {
                $updateData['paid_amount'] = $request->total_harga;
            }

            $transaction->update($updateData);

            // Reload fresh instance
            $transaction = $transaction->fresh();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => $transaction,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified expense transaction.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            // Get owner_id based on user role
            $ownerId = $user->role_id === 3 ? ($user->owner ? $user->owner->id : null) : $user->id;
            
            $transaction = PosTransaksi::where('owner_id', $ownerId)
                ->where('id', $id)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
