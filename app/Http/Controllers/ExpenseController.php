<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosToko;
use App\Models\PosKategoriExpense;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
     * Display a listing of expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('expense.read');
        
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $query = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->with(['toko', 'kategoriExpense']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%')
                  ->orWhereHas('toko', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('kategoriExpense', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by toko
        if ($request->filled('toko_id')) {
            $query->where('pos_toko_id', $request->toko_id);
        }

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->where('pos_kategori_expense_id', $request->kategori_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        // Get filter options
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $kategoris = PosKategoriExpense::where('owner_id', $ownerId)
            ->orWhere('is_global', 1)
            ->get();

        return view('pages.expense.index', compact('expenses', 'tokos', 'kategoris', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new expense
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check create permission
        if (!PermissionService::check('expense.create')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk membuat expense baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $kategoris = PosKategoriExpense::where('owner_id', $ownerId)
            ->orWhere('is_global', 1)
            ->get();

        // Generate invoice number
        $invoiceNumber = $this->generateExpenseInvoice($ownerId);

        return view('pages.expense.create', compact('tokos', 'kategoris', 'invoiceNumber'));
    }

    /**
     * Store a newly created expense
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check create permission
        if (!PermissionService::check('expense.create')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk membuat expense baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Check if reached limit
        $currentCount = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->count();

        if (PermissionService::isReachedLimit('expense.create', $currentCount)) {
            return redirect()->back()
                ->with('error', 'Anda sudah mencapai batas maksimal expense yang diizinkan.')
                ->withInput();
        }

        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_kategori_expense_id' => 'required|exists:pos_kategori_expense,id',
            'invoice' => 'required|string|max:255',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled',
            'metode_pembayaran' => 'required|in:tunai,non tunai',
        ]);

        DB::beginTransaction();
        try {
            $status = strtolower(trim($validated['status']));
            
            \Log::info('[EXPENSE CREATE] Starting expense creation', [
                'invoice' => $validated['invoice'],
                'status' => $status,
                'total_harga' => $validated['total_harga'],
                'owner_id' => $ownerId,
            ]);
            
            // Create expense transaction
            $expense = PosTransaksi::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_kategori_expense_id' => $validated['pos_kategori_expense_id'],
                'is_transaksi_masuk' => 0, // Expense is outgoing transaction
                'invoice' => $validated['invoice'],
                'total_harga' => $validated['total_harga'],
                'keterangan' => $validated['keterangan'],
                'status' => $status,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'payment_status' => 'paid',
                'paid_amount' => $validated['total_harga'],
            ]);

            \Log::info('[EXPENSE CREATE] Expense created successfully', [
                'id' => $expense->id,
                'invoice' => $expense->invoice,
                'status' => $expense->status,
            ]);

            DB::commit();

            return redirect()->route('expense.index')
                ->with('success', 'Expense berhasil ditambahkan dengan status: ' . ucfirst($status));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[EXPENSE CREATE] Error creating expense', [
                'message' => $e->getMessage(),
                'invoice' => $validated['invoice'] ?? null,
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified expense
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check update permission
        if (!PermissionService::check('expense.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah expense.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $expense = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->with(['toko', 'kategoriExpense'])
            ->findOrFail($id);

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $kategoris = PosKategoriExpense::where('owner_id', $ownerId)
            ->orWhere('is_global', 1)
            ->get();

        return view('pages.expense.edit', compact('expense', 'tokos', 'kategoris'));
    }

    /**
     * Update the specified expense
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check update permission
        if (!PermissionService::check('expense.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah expense.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $expense = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->findOrFail($id);

        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_kategori_expense_id' => 'required|exists:pos_kategori_expense,id',
            'invoice' => 'required|string|max:255',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled',
            'metode_pembayaran' => 'required|in:tunai,non tunai',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = strtolower(trim($expense->status));
            $newStatus = strtolower(trim($validated['status']));
            
            \Log::info('[EXPENSE UPDATE] Starting expense update', [
                'id' => $id,
                'invoice' => $expense->invoice,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'owner_id' => $ownerId,
            ]);

            // Update all fields
            $expense->update([
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_kategori_expense_id' => $validated['pos_kategori_expense_id'],
                'invoice' => $validated['invoice'],
                'total_harga' => $validated['total_harga'],
                'keterangan' => $validated['keterangan'],
                'status' => $newStatus,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'paid_amount' => $validated['total_harga'],
            ]);

            \Log::info('[EXPENSE UPDATE] Expense updated successfully', [
                'id' => $id,
                'invoice' => $expense->invoice,
                'new_status' => $newStatus,
            ]);

            DB::commit();

            return redirect()->route('expense.index')
                ->with('success', 'Expense berhasil diupdate ke status: ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[EXPENSE UPDATE] Error updating expense', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified expense
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check delete permission
        if (!PermissionService::check('expense.delete')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk menghapus expense.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $expense = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            \Log::info('[EXPENSE DELETE] Starting expense deletion', [
                'id' => $id,
                'invoice' => $expense->invoice,
                'status' => $expense->status,
                'owner_id' => $ownerId,
            ]);

            $invoiceNumber = $expense->invoice;
            $expense->delete();

            \Log::info('[EXPENSE DELETE] Expense deleted successfully', [
                'id' => $id,
                'invoice' => $invoiceNumber,
            ]);

            DB::commit();

            return redirect()->route('expense.index')
                ->with('success', 'Expense berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[EXPENSE DELETE] Error deleting expense', [
                'id' => $id,
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete expenses
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        // Check delete permission
        if (!PermissionService::check('expense.delete')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk menghapus expense.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pos_transaksi,id'
        ]);

        DB::beginTransaction();
        try {
            $invoices = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->whereIn('id', $request->ids)
                ->pluck('invoice')
                ->toArray();

            \Log::info('[EXPENSE BULK DELETE] Starting bulk deletion', [
                'count' => count($request->ids),
                'invoices' => $invoices,
                'owner_id' => $ownerId,
            ]);

            PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', 0)
                ->whereNotNull('pos_kategori_expense_id')
                ->whereIn('id', $request->ids)
                ->delete();

            \Log::info('[EXPENSE BULK DELETE] Expenses deleted successfully', [
                'count' => count($request->ids),
            ]);

            DB::commit();

            return redirect()->route('expense.index')
                ->with('success', count($request->ids) . ' expense berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[EXPENSE BULK DELETE] Error deleting expenses', [
                'count' => count($request->ids),
                'message' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status of an expense transaction via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->findOrFail($id);

        $oldStatus = $transaksi->status;
        $newStatus = strtolower(trim($request->status));

        DB::beginTransaction();
        try {
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => 'paid',
                    'paid_amount' => $transaksi->total_harga,
                ]);
            } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => $newStatus === 'cancelled' ? 'cancelled' : 'unpaid',
                    'paid_amount' => 0,
                ]);
            } else {
                $transaksi->update([
                    'status' => $newStatus,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status expense berhasil diubah menjadi ' . ucfirst($newStatus),
                    'status' => $newStatus,
                ]);
            }

            return redirect()->back()->with('success', 'Status expense berhasil diubah menjadi ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
