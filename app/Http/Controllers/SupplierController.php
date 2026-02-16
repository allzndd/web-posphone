<?php

namespace App\Http\Controllers;

use App\Models\PosSupplier;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hasAccessRead = PermissionService::check('supplier.read');
        
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $perPage = $request->get('per_page', 10);
        
        $query = PosSupplier::where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc');

        // Search by name or phone
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nomor_hp', 'like', '%' . $searchTerm . '%');
            });
        }

        $suppliers = $query->paginate($perPage);

        return view('pages.supplier.index', compact('suppliers', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!PermissionService::check('supplier.create')) {
            return redirect('/')->with('error', 'You do not have permission to create suppliers');
        }
        return view('pages.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!PermissionService::check('supplier.create')) {
            return redirect('/')->with('error', 'You do not have permission to create suppliers');
        }
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $validated['owner_id'] = $user->owner ? $user->owner->id : null;
        $validated['slug'] = Str::slug($validated['nama']);

        PosSupplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosSupplier $supplier)
    {
        if (!PermissionService::check('supplier.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit suppliers');
        }
        return view('pages.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosSupplier $supplier)
    {
        if (!PermissionService::check('supplier.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit suppliers');
        }
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['nama']);

        $supplier->update($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosSupplier $supplier)
    {
        if (!PermissionService::check('supplier.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete suppliers');
        }
        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier deleted successfully');
    }

    /**
     * Bulk delete multiple suppliers.
     */
    public function bulkDestroy(Request $request)
    {
        if (!PermissionService::check('supplier.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete suppliers');
        }
        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Please select at least one supplier to delete');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $deletedCount = PosSupplier::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('supplier.index')
            ->with('success', $deletedCount . ' supplier(s) deleted successfully');
    }
}
