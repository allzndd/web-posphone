<?php

namespace App\Http\Controllers;

use App\Models\ProdukStok;
use App\Models\PosProduk;
use App\Models\PosToko;
use Illuminate\Http\Request;

class ProdukStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $stok = ProdukStok::with(['produk', 'toko'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('pages.produk-stok.index', compact('stok'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = PosProduk::orderBy('nama')->get();
        $toko = PosToko::orderBy('nama')->get();

        return view('pages.produk-stok.create', compact('produk', 'toko'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pos_produk_id' => 'required|exists:pos_produk,id',
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'stok' => 'required|integer|min:0',
        ]);

        $validated['owner_id'] = auth()->id();

        ProdukStok::create($validated);

        return redirect()->route('produk-stok.index')
            ->with('success', 'Product stock created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProdukStok $produkStok)
    {
        $produkStok->load(['produk', 'toko']);
        return view('pages.produk-stok.show', compact('produkStok'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukStok $produkStok)
    {
        $produk = PosProduk::orderBy('nama')->get();
        $toko = PosToko::orderBy('nama')->get();

        return view('pages.produk-stok.edit', compact('produkStok', 'produk', 'toko'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProdukStok $produkStok)
    {
        $validated = $request->validate([
            'pos_produk_id' => 'required|exists:pos_produk,id',
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'stok' => 'required|integer|min:0',
        ]);

        $produkStok->update($validated);

        return redirect()->route('produk-stok.index')
            ->with('success', 'Product stock updated successfully');
    }

    /**
     * Update stock inline from index page.
     */
    public function updateInline(Request $request, ProdukStok $produkStok)
    {
        $validated = $request->validate([
            'stok' => 'required|integer|min:0',
        ]);

        $produkStok->update($validated);

        return redirect()->route('produk-stok.index')
            ->with('success', 'Stock updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProdukStok $produkStok)
    {
        $produkStok->delete();

        return redirect()->route('produk-stok.index')
            ->with('success', 'Product stock deleted successfully');
    }
}
