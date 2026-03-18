<?php

namespace App\Http\Controllers;

use App\Models\ProdukStok;
use App\Models\PosProduk;
use App\Models\PosToko;
use App\Services\InventoryAvailabilityService;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class ProdukStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('produk-stok.read');
        
        $perPage = $request->get('per_page', 10);
        $searchTerm = $request->get('search');
        
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $stok = ProdukStok::with(['produk.merk', 'toko'])
            ->where('owner_id', $ownerId)
            ->when($searchTerm, function($query) use ($searchTerm) {
                return $query->where(function($q) use ($searchTerm) {
                    $q->whereHas('produk', function($subQuery) use ($searchTerm) {
                        $subQuery->where('nama', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('toko', function($subQuery) use ($searchTerm) {
                        $subQuery->where('nama', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhere('merk_name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return view('pages.produk-stok.index', compact('stok', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission to create
        if (!PermissionService::check('produk-stok.create')) {
            return redirect()->route('produk-stok.index')->with('error', 'Anda tidak memiliki akses untuk membuat stok baru.');
        }

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
        // Check permission to create
        if (!PermissionService::check('produk-stok.create')) {
            return redirect()->route('produk-stok.index')->with('error', 'Anda tidak memiliki akses untuk membuat stok baru.');
        }

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
     * Display the specified resource with all related products (AJAX JSON response).
     */
    public function show(ProdukStok $produkStok)
    {
        // Check if it's an AJAX request
        if (request()->ajax()) {
            $produkStok->load(['produk.merk', 'toko']);
            
            $primaryProduk = $produkStok->produk;
            
            // If the representative product was deleted, show snapshot data
            if (!$primaryProduk) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'produk_stok_id' => $produkStok->id,
                        'merk_nama' => $produkStok->grouped_name ?? $produkStok->merk_name ?? 'Unknown',
                        'toko_nama' => $produkStok->toko->nama ?? '-',
                        'total_stok' => $produkStok->stok,
                        'produk_list' => [],
                    ]
                ]);
            }
            
            $terkaitProduk = InventoryAvailabilityService::getAvailableProductsForStockEntry(
                    $produkStok,
                    ['merk', 'warna', 'ram', 'penyimpanan']
                )
                ->map(function($produk) {
                    // Use same logic as halaman produk (produk index)
                    // RAM: kapasitas (not nama)
                    $ram = ($produk->pos_ram_id && $produk->ram) ? $produk->ram->kapasitas . ' GB' : '-';
                    
                    // Storage: kapasitas (not nama)
                    $penyimpanan = ($produk->pos_penyimpanan_id && $produk->penyimpanan) ? $produk->penyimpanan->kapasitas . ' GB' : '-';
                    
                    // Color: warna field (not nama)
                    $warna = ($produk->pos_warna_id && $produk->warna) ? $produk->warna->warna : '-';
                    
                    return [
                        'id' => $produk->id,
                        'nama' => $produk->nama,
                        'imei' => $produk->imei,
                        'warna' => $warna,
                        'ram' => $ram,
                        'penyimpanan' => $penyimpanan,
                        'harga_beli' => $produk->harga_beli,
                        'harga_jual' => $produk->harga_jual,
                        'battery_health' => $produk->battery_health,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'produk_stok_id' => $produkStok->id,
                    'merk_nama' => $produkStok->grouped_name,
                    'toko_nama' => $produkStok->toko->nama ?? '-',
                    'total_stok' => $produkStok->stok,
                    'produk_list' => $terkaitProduk,
                ]
            ]);
        }
        
        // Non-AJAX request - redirect to index
        return redirect()->route('produk-stok.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProdukStok $produkStok)
    {
        // Check permission to update
        if (!PermissionService::check('produk-stok.update')) {
            return redirect()->route('produk-stok.index')->with('error', 'Anda tidak memiliki akses untuk mengedit stok.');
        }

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
        // Check permission to update
        if (!PermissionService::check('produk-stok.update')) {
            return redirect()->route('produk-stok.index')->with('error', 'Anda tidak memiliki akses untuk mengubah stok.');
        }

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
     * This deletes the STOCK ENTRY only, NOT the individual products.
     * Products remain in pos_produk table - only the stock tracking is removed.
     */
    public function destroy(ProdukStok $produkStok)
    {
        // Check permission to delete
        if (!PermissionService::check('produk-stok.delete')) {
            return redirect()->route('produk-stok.index')->with('error', 'Anda tidak memiliki akses untuk menghapus stok.');
        }

        $user = auth()->user();
        $ownerId = $produkStok->owner_id;
        $produk = $produkStok->produk;
        $tokoId = $produkStok->pos_toko_id;
        $stokCount = $produkStok->stok;

        // Create log stok before deletion
        if ($produk) {
            \App\Models\LogStok::create([
                'owner_id' => $ownerId,
                'pos_produk_id' => $produkStok->pos_produk_id,
                'pos_toko_id' => $tokoId,
                'stok_sebelum' => $stokCount,
                'stok_sesudah' => 0,
                'perubahan' => -$stokCount,
                'tipe' => 'keluar',
                'referensi' => 'Hapus Stok',
                'keterangan' => 'Stok dihapus dari halaman Produk Stok',
                'pos_pengguna_id' => $user->id,
            ]);
        }

        // Delete only the stock entry - products remain in pos_produk
        $produkStok->delete();

        return redirect()->route('produk-stok.index')
            ->with('success', 'Stock entry deleted successfully. Products are still available in Produk page.');
    }

    /**
     * Bulk delete multiple stock records.
     * This deletes the STOCK ENTRIES only, NOT the individual products.
     * Products remain in pos_produk table - only the stock tracking is removed.
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('produk-stok.delete')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus stok.');
        }

        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu item untuk dihapus');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get all stock entries to be deleted
        $stokEntries = ProdukStok::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->with('produk')
            ->get();

        // Create log for each stock entry being deleted
        foreach ($stokEntries as $stokEntry) {
            if ($stokEntry->produk) {
                \App\Models\LogStok::create([
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $stokEntry->pos_produk_id,
                    'pos_toko_id' => $stokEntry->pos_toko_id,
                    'stok_sebelum' => $stokEntry->stok,
                    'stok_sesudah' => 0,
                    'perubahan' => -$stokEntry->stok,
                    'tipe' => 'keluar',
                    'referensi' => 'Hapus Stok Massal',
                    'keterangan' => 'Bulk delete stok dari halaman Produk Stok',
                    'pos_pengguna_id' => $user->id,
                ]);
            }
        }

        // Delete only the stock entries - products remain in pos_produk
        $deletedCount = ProdukStok::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('produk-stok.index')
            ->with('success', $deletedCount . ' item stok berhasil dihapus. Produk tetap tersedia di halaman Produk.');
    }
}
