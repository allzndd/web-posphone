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
        $searchTerm = $request->get('search');
        
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $stok = ProdukStok::with(['produk.merk', 'toko'])
            ->where('owner_id', $ownerId)
            ->when($searchTerm, function($query) use ($searchTerm) {
                return $query->whereHas('produk', function($subQuery) use ($searchTerm) {
                    $subQuery->where('nama', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('toko', function($subQuery) use ($searchTerm) {
                    $subQuery->where('nama', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('pages.produk-stok.index', compact('stok'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $produk = PosProduk::where('owner_id', $ownerId)
            ->with(['merk'])
            ->get()
            ->sortBy('display_name');
        $toko = PosToko::where('owner_id', $ownerId)
            ->orderBy('nama')
            ->get();

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

        $user = auth()->user();
        $validated['owner_id'] = $user->owner ? $user->owner->id : null;

        $produkStok = ProdukStok::create($validated);

        // Create log stok
        \App\Models\LogStok::create([
            'owner_id' => $validated['owner_id'],
            'pos_produk_id' => $validated['pos_produk_id'],
            'pos_toko_id' => $validated['pos_toko_id'],
            'stok_sebelum' => 0,
            'stok_sesudah' => $validated['stok'],
            'perubahan' => $validated['stok'],
            'tipe' => 'masuk',
            'referensi' => 'Stok Manual',
            'keterangan' => 'Penambahan stok manual',
            'pos_pengguna_id' => $user->id,
        ]);

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
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $produk = PosProduk::where('owner_id', $ownerId)
            ->with(['merk'])
            ->get()
            ->sortBy('display_name');
        $toko = PosToko::where('owner_id', $ownerId)
            ->orderBy('nama')
            ->get();

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

        $stokLama = $produkStok->stok;
        $stokBaru = $validated['stok'];
        $perubahan = $stokBaru - $stokLama;

        $produkStok->update($validated);

        // Create log stok if there's a change
        if ($perubahan != 0) {
            $user = auth()->user();
            \App\Models\LogStok::create([
                'owner_id' => $produkStok->owner_id,
                'pos_produk_id' => $produkStok->pos_produk_id,
                'pos_toko_id' => $produkStok->pos_toko_id,
                'stok_sebelum' => $stokLama,
                'stok_sesudah' => $stokBaru,
                'perubahan' => $perubahan,
                'tipe' => $perubahan > 0 ? 'masuk' : 'keluar',
                'referensi' => 'Update Stok',
                'keterangan' => 'Perubahan stok manual via edit',
                'pos_pengguna_id' => $user->id,
            ]);
        }

        return redirect()->route('produk-stok.index')
            ->with('success', 'Product stock updated successfully');
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

    /**
     * Bulk delete multiple stock records.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu item untuk dihapus');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $deletedCount = ProdukStok::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('produk-stok.index')
            ->with('success', $deletedCount . ' item stok berhasil dihapus');
    }
}
