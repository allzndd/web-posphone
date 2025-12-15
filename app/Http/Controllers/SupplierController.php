<?php

namespace App\Http\Controllers;

use App\Models\PosSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $query = PosSupplier::query()
            ->orderBy('created_at', 'desc');

        // Search by name
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Search by phone
        if ($request->filled('nomor_hp')) {
            $query->where('nomor_hp', 'like', '%' . $request->nomor_hp . '%');
        }

        $suppliers = $query->paginate($perPage);

        return view('pages.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return view('pages.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosSupplier $supplier)
    {
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
        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier deleted successfully');
    }
}
