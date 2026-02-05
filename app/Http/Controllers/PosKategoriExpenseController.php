<?php

namespace App\Http\Controllers;

use App\Models\PosKategoriExpense;
use App\Models\User;
use Illuminate\Http\Request;

class PosKategoriExpenseController extends Controller
{
    /**
     * Display a listing of the expense categories.
     */
    public function index()
    {
        $query = PosKategoriExpense::with('owner');
        
        // If superadmin, only show global items
        if (auth()->user()->role_id === 1) {
            $query->where('is_global', 1);
        }
        // If owner, show global items and their own items
        elseif (auth()->user()->role_id === 2) {
            $query->where(function($q) {
                $q->where('is_global', 1)
                  ->orWhere('owner_id', auth()->id());
            });
        } elseif (auth()->user()->role_id === 3) { // role_id 3 = admin
            // Admin can only see global items
            $query->where('is_global', 1);
        }
        
        // Fuzzy search - search across all database data before pagination
        if (request('nama')) {
            $searchTerm = request('nama');
            // Split search term into words for better fuzzy matching
            $words = explode(' ', trim($searchTerm));
            
            $query->where(function($q) use ($words, $searchTerm) {
                // First, try exact partial match on the full search term
                $q->where('nama', 'LIKE', '%' . $searchTerm . '%');
                
                // Also match if all individual words are found
                foreach ($words as $word) {
                    $q->orWhere('nama', 'LIKE', '%' . $word . '%');
                }
            });
        }
        
        $perPage = request('per_page', 10);
        $kategoriExpenses = $query->paginate($perPage);
        return view('pages.pos-kategori-expense.index', compact('kategoriExpenses'));
    }

    /**
     * Show the form for creating a new expense category.
     */
    public function create()
    {
        // Only super admin can select owner; for other roles, owner_id is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-kategori-expense.create', compact('owners'));
    }

    /**
     * Store a newly created expense category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // For superadmin, set owner_id to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['owner_id'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set owner_id to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, set is_global to 0 and owner_id to their ID
        elseif (auth()->user()->role_id === 3) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }

        PosKategoriExpense::create($validated);

        return redirect()->route('pos-kategori-expense.index')
            ->with('success', 'Kategori Expense berhasil ditambahkan');
    }

    /**
     * Display the specified expense category.
     */
    public function show(PosKategoriExpense $posKategoriExpense)
    {
        return view('pages.pos-kategori-expense.show', compact('posKategoriExpense'));
    }

    /**
     * Show the form for editing the specified expense category.
     */
    public function edit(PosKategoriExpense $posKategoriExpense)
    {
        // Check authorization for owner - only can edit their own items
        if (auth()->user()->role_id === 2 && $posKategoriExpense->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only super admin can select owner; for other roles, owner_id is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-kategori-expense.edit', compact('posKategoriExpense', 'owners'));
    }

    /**
     * Update the specified expense category in storage.
     */
    public function update(Request $request, PosKategoriExpense $posKategoriExpense)
    {
        // Check authorization for owner - only can update their own items, not global items
        if (auth()->user()->role_id === 2 && $posKategoriExpense->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // For superadmin, set owner_id to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['owner_id'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set owner_id to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, set is_global to 0 and owner_id to their ID
        elseif (auth()->user()->role_id === 3) {
            $validated['owner_id'] = auth()->id();
            $validated['is_global'] = 0;
        }

        $posKategoriExpense->update($validated);

        return redirect()->route('pos-kategori-expense.index')
            ->with('success', 'Kategori Expense berhasil diperbarui');
    }

    /**
     * Remove the specified expense category from storage.
     */
    public function destroy(PosKategoriExpense $posKategoriExpense)
    {
        // Check authorization for owner - only can delete their own items, not global items
        if (auth()->user()->role_id === 2 && $posKategoriExpense->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $posKategoriExpense->delete();

        return redirect()->route('pos-kategori-expense.index')
            ->with('success', 'Kategori Expense berhasil dihapus');
    }

    /**
     * Delete multiple expense categories
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-kategori-expense.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        $query = PosKategoriExpense::whereIn('id', $ids);
        
        // If owner, only allow deleting their own items
        if (auth()->user()->role_id === 2) {
            $query->where('owner_id', auth()->id());
        }
        
        $query->delete();

        return redirect()->route('pos-kategori-expense.index')
            ->with('success', 'Kategori Expense berhasil dihapus');
    }
}
