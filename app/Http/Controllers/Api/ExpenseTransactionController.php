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
     * Display a listing of expense transactions.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = PosTransaksi::where('owner_id', $user->id)
                ->orderBy('created_at', 'desc');
            
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
            
            // Calculate statistics
            $totalTransactions = PosTransaksi::where('owner_id', $user->id)->count();
            $totalExpense = PosTransaksi::where('owner_id', $user->id)->sum('total_harga');
            
            $data = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'owner_id' => $transaction->owner_id,
                    'pos_toko_id' => $transaction->pos_toko_id,
                    'pos_kategori_expense_id' => $transaction->pos_kategori_expense_id,
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
                    'kategori_expense_name' => $transaction->kategoriExpense ? $transaction->kategoriExpense->nama : null,
                    'toko_name' => $transaction->toko ? $transaction->toko->nama : null,
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
            
            // Generate invoice number if not provided
            $invoiceNumber = $request->invoice;
            if (!$invoiceNumber) {
                $lastTransaction = PosTransaksi::where('owner_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->first();
                
                $invoiceNumber = 'EXP-' . date('Ymd') . '-' . str_pad(($lastTransaction ? $lastTransaction->id + 1 : 1), 4, '0', STR_PAD_LEFT);
            }

            // Default status to pending if not provided
            $status = $request->status ?? 'pending';

            $transaction = PosTransaksi::create([
                'owner_id' => $user->id,
                'pos_toko_id' => $request->pos_toko_id,
                'pos_kategori_expense_id' => $request->pos_kategori_expense_id,
                'invoice' => $invoiceNumber,
                'total_harga' => $request->total_harga,
                'keterangan' => $request->keterangan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $status,
                'payment_status' => $status === 'completed' ? 'paid' : 'unpaid',
                'paid_amount' => $status === 'completed' ? $request->total_harga : 0,
            ]);

            // Reload fresh instance
            $transaction = PosTransaksi::find($transaction->id);

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction,
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
            
            $transaction = PosTransaksi::where('owner_id', $user->id)
                ->where('id', $id)
                ->with(['kategoriExpense', 'toko'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction retrieved successfully',
                'data' => [
                    'id' => $transaction->id,
                    'owner_id' => $transaction->owner_id,
                    'pos_toko_id' => $transaction->pos_toko_id,
                    'pos_kategori_expense_id' => $transaction->pos_kategori_expense_id,
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
                    'kategori_expense_name' => $transaction->kategoriExpense ? $transaction->kategoriExpense->nama : null,
                    'toko_name' => $transaction->toko ? $transaction->toko->nama : null,
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
            
            $transaction = PosTransaksi::where('owner_id', $user->id)
                ->where('id', $id)
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
            
            $transaction = PosTransaksi::where('owner_id', $user->id)
                ->where('id', $id)
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
