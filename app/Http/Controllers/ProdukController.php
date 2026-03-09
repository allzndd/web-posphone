<?php

namespace App\Http\Controllers;

use App\Models\PosProduk;
use App\Models\PosProdukBiayaTambahan;
use App\Models\PosProdukMerk;
use App\Models\PosService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('produk.read');
        
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $searchQuery = $request->input('search') ?? $request->input('nama');

        // Get merk IDs that have stock > 0 (stock is grouped by merk)
        // First get the representative product IDs with stock, then get their merk IDs
        $merkIdsWithStock = \App\Models\ProdukStok::where('owner_id', $ownerId)
            ->where('stok', '>', 0)
            ->with('produk')
            ->get()
            ->map(function($stok) {
                return $stok->produk ? $stok->produk->pos_produk_merk_id : null;
            })
            ->filter()
            ->unique();
        
        // Get ALL products whose merk has stock (stock is grouped by merk)
        $produk = PosProduk::where('owner_id', $ownerId)
            ->with(['merk', 'stok', 'ram', 'penyimpanan', 'warna'])
            ->whereIn('pos_produk_merk_id', $merkIdsWithStock)
            ->when($searchQuery, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('imei', 'like', '%' . $search . '%')
                        ->orWhere('battery_health', 'like', '%' . $search . '%')
                        ->orWhere('harga_jual', 'like', '%' . $search . '%')
                        ->orWhereHas('ram', function ($rq) use ($search) {
                            $rq->where('kapasitas', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('penyimpanan', function ($rq) use ($search) {
                            $rq->where('kapasitas', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('warna', function ($rq) use ($search) {
                            $rq->where('warna', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        // Return JSON response if AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('pages.produk.partials.table-body', compact('produk'))->render(),
                'pagination' => view('pages.produk.partials.pagination', compact('produk', 'searchQuery'))->render(),
            ]);
        }

        return view('pages.produk.index', compact('produk', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check permission to create
        if (!PermissionService::check('produk.create')) {
            return redirect()->route('produk.index')->with('error', 'Anda tidak memiliki akses untuk membuat produk baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $merks = PosProdukMerk::where('owner_id', $ownerId)->get();

        return view('pages.produk.create', compact('merks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check permission to create
        if (!PermissionService::check('produk.create')) {
            return redirect()->route('produk.index')->with('error', 'Anda tidak memiliki akses untuk membuat produk baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'nullable|string|max:255',
            'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
            'product_type' => 'required|string|in:electronic,accessories',
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'penyimpanan' => 'nullable|string|max:255',
            'battery_health' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'cost_names.*' => 'nullable|string',
            'cost_amounts.*' => 'nullable|numeric',
            'imei' => $request->product_type === 'electronic' ? 'required|string|max:255' : 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:45',
        ]);

        // Convert cost arrays to JSON format for biaya_tambahan
        $biayaTambahan = null;
        if ($request->has('cost_names') && $request->has('cost_amounts')) {
            $names = array_filter($request->cost_names, fn($value) => !empty($value));
            $amounts = array_filter($request->cost_amounts, fn($value) => !empty($value));
            
            if (!empty($names) && !empty($amounts) && count($names) === count($amounts)) {
                $biayaTambahan = array_combine(array_values($names), array_values($amounts));
            }
        }

        // Auto-generate nama if not provided
        $nama = $request->nama;
        if (empty($nama)) {
            $merk = PosProdukMerk::find($request->pos_produk_merk_id);
            $namaParts = [];
            
            if ($merk && $merk->nama) {
                $namaParts[] = $merk->nama;
            }
            
            if (!empty($request->warna)) {
                $namaParts[] = $request->warna;
            }
            
            if (!empty($request->penyimpanan)) {
                $namaParts[] = $request->penyimpanan . 'GB';
            }
            
            $nama = !empty($namaParts) ? implode(' ', $namaParts) : 'Produk Baru';
        }

        // Lookup or create warna to get pos_warna_id
        $posWarnaId = null;
        if (!empty($request->warna)) {
            $warna = \App\Models\PosWarna::firstOrCreate(
                ['warna' => $request->warna, 'id_owner' => $ownerId],
                ['is_global' => 0]
            );
            $posWarnaId = $warna->id;
        }

        // Lookup or create penyimpanan to get pos_penyimpanan_id
        $posPenyimpananId = null;
        if (!empty($request->penyimpanan)) {
            $penyimpanan = \App\Models\PosPenyimpanan::firstOrCreate(
                ['kapasitas' => $request->penyimpanan, 'id_owner' => $ownerId],
                []
            );
            $posPenyimpananId = $penyimpanan->id;
        }

        $produk = PosProduk::create([
            'owner_id' => $ownerId,
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'product_type' => $request->product_type,
            'nama' => $nama,
            'deskripsi' => $request->deskripsi,
            'pos_warna_id' => $posWarnaId,
            'pos_penyimpanan_id' => $posPenyimpananId,
            'battery_health' => $request->battery_health,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'biaya_tambahan' => $biayaTambahan,
            'imei' => $request->imei,
            'aksesoris' => $request->aksesoris,
        ]);

        // Create or update stock entry for all stores (GROUPED by merk + store)
        $toko = \App\Models\PosToko::where('owner_id', $ownerId)->get();
        $merkId = $produk->pos_produk_merk_id;
        
        foreach ($toko as $store) {
            // Check if stock entry already exists for this MERK + STORE (grouped stock)
            $existingStok = \App\Models\ProdukStok::where('owner_id', $ownerId)
                ->where('pos_toko_id', $store->id)
                ->whereHas('produk', function($query) use ($merkId) {
                    $query->where('pos_produk_merk_id', $merkId);
                })
                ->first();

            if ($existingStok) {
                // Increment existing stock
                $stokSebelum = $existingStok->stok;
                $existingStok->update(['stok' => $stokSebelum + 1]);
                
                \App\Models\LogStok::create([
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $produk->id,
                    'pos_toko_id' => $store->id,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSebelum + 1,
                    'perubahan' => 1,
                    'tipe' => 'masuk',
                    'referensi' => 'Produk Baru: ' . $produk->nama,
                    'keterangan' => 'Tambah stok produk baru (grouped by merk)',
                    'pos_pengguna_id' => $user->id,
                ]);
            } else {
                // Create new stock entry with this product as representative
                \App\Models\ProdukStok::create([
                    'owner_id' => $ownerId,
                    'pos_toko_id' => $store->id,
                    'pos_produk_id' => $produk->id,
                    'stok' => 1,
                ]);

                \App\Models\LogStok::create([
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $produk->id,
                    'pos_toko_id' => $store->id,
                    'stok_sebelum' => 0,
                    'stok_sesudah' => 1,
                    'perubahan' => 1,
                    'tipe' => 'masuk',
                    'referensi' => 'Produk Baru: ' . $produk->nama,
                    'keterangan' => 'Stok awal produk baru',
                    'pos_pengguna_id' => $user->id,
                ]);
            }
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  PosProduk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(PosProduk $produk)
    {
        $produk->load(['merk', 'ram', 'penyimpanan', 'warna', 'biayaTambahanItems']);
        
        return view('pages.produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosProduk $produk)
    {
        // Check permission to update
        if (!PermissionService::check('produk.update')) {
            return redirect()->route('produk.index')->with('error', 'Anda tidak memiliki akses untuk mengedit produk.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $merks = PosProdukMerk::where('owner_id', $ownerId)->get();
        
        // Load biaya tambahan from database
        $biayaTambahanItems = DB::table('pos_produk_biaya_tambahan')
            ->where('pos_produk_id', $produk->id)
            ->get();

        return view('pages.produk.edit', compact('produk', 'merks', 'biayaTambahanItems'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PosProduk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosProduk $produk)
    {
        // Check permission to update
        if (!PermissionService::check('produk.update')) {
            return redirect()->route('produk.index')->with('error', 'Anda tidak memiliki akses untuk mengubah produk.');
        }

        $request->validate([
            'nama' => 'nullable|string|max:255',
            'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
            'product_type' => 'required|string|in:electronic,accessories',
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'penyimpanan' => 'nullable|string|max:255',
            'battery_health' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'cost_names.*' => 'nullable|string',
            'cost_amounts.*' => 'nullable|numeric',
            'imei' => $request->product_type === 'electronic' ? 'required|string|max:255' : 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:45',
        ]);

        // Convert cost arrays to JSON format for biaya_tambahan
        $biayaTambahan = null;
        if ($request->has('cost_names') && $request->has('cost_amounts')) {
            $names = array_filter($request->cost_names, fn($value) => !empty($value));
            $amounts = array_filter($request->cost_amounts, fn($value) => !empty($value));
            
            if (!empty($names) && !empty($amounts) && count($names) === count($amounts)) {
                $biayaTambahan = array_combine(array_values($names), array_values($amounts));
            }
        }

        // Auto-generate nama if not provided
        $nama = $request->nama;
        if (empty($nama)) {
            $merk = PosProdukMerk::find($request->pos_produk_merk_id);
            $namaParts = [];
            
            if ($merk && $merk->nama) {
                $namaParts[] = $merk->nama;
            }
            
            if (!empty($request->warna)) {
                $namaParts[] = $request->warna;
            }
            
            if (!empty($request->penyimpanan)) {
                $namaParts[] = $request->penyimpanan . 'GB';
            }
            
            $nama = !empty($namaParts) ? implode(' ', $namaParts) : 'Produk';
        }

        // Get owner_id for lookup
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Lookup or create warna to get pos_warna_id
        $posWarnaId = null;
        if (!empty($request->warna)) {
            $warna = \App\Models\PosWarna::firstOrCreate(
                ['warna' => $request->warna, 'id_owner' => $ownerId],
                ['is_global' => 0]
            );
            $posWarnaId = $warna->id;
        }

        // Lookup or create penyimpanan to get pos_penyimpanan_id
        $posPenyimpananId = null;
        if (!empty($request->penyimpanan)) {
            $penyimpanan = \App\Models\PosPenyimpanan::firstOrCreate(
                ['kapasitas' => $request->penyimpanan, 'id_owner' => $ownerId],
                []
            );
            $posPenyimpananId = $penyimpanan->id;
        }

        $produk->update([
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'product_type' => $request->product_type,
            'nama' => $nama,
            'deskripsi' => $request->deskripsi,
            'pos_warna_id' => $posWarnaId,
            'pos_penyimpanan_id' => $posPenyimpananId,
            'battery_health' => $request->battery_health,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'biaya_tambahan' => $biayaTambahan,
            'imei' => $request->imei,
            'aksesoris' => $request->aksesoris,
        ]);

        // Sync biaya tambahan to pos_produk_biaya_tambahan table
        // Delete existing entries
        DB::table('pos_produk_biaya_tambahan')->where('pos_produk_id', $produk->id)->delete();
        
        // Insert new entries if product is electronic
        if ($request->product_type === 'electronic' && $request->has('cost_names') && $request->has('cost_amounts')) {
            $names = $request->cost_names;
            $amounts = $request->cost_amounts;
            
            for ($i = 0; $i < count($names); $i++) {
                if (!empty($names[$i]) && !empty($amounts[$i]) && $amounts[$i] > 0) {
                    DB::table('pos_produk_biaya_tambahan')->insert([
                        'pos_produk_id' => $produk->id,
                        'nama' => $names[$i],
                        'harga' => $amounts[$i],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     * Deletes individual product and REDUCES stock count (does not delete entire stock entry)
     *
     * @param  PosProduk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosProduk $produk)
    {
        // Check permission to delete
        if (!PermissionService::check('produk.delete')) {
            return redirect()->route('produk.index')->with('error', 'Anda tidak memiliki akses untuk menghapus produk.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;
        $merkId = $produk->pos_produk_merk_id;

        // Find another product with same merk to use as new representative (exclude current product)
        $newRepresentativeProduk = PosProduk::where('owner_id', $ownerId)
            ->where('pos_produk_merk_id', $merkId)
            ->where('id', '!=', $produk->id) // Exclude product being deleted
            ->orderBy('id', 'asc') // Use oldest remaining product
            ->first();

        // Find ALL stock entries for this MERK (not just those referencing this product)
        // Stock is grouped by merk, so we need to find by merk_id
        $stokEntriesForMerk = \App\Models\ProdukStok::where('owner_id', $ownerId)
            ->whereHas('produk', function($query) use ($merkId) {
                $query->where('pos_produk_merk_id', $merkId);
            })
            ->get();

        foreach ($stokEntriesForMerk as $stokEntry) {
            // Create log stok for stock reduction
            \App\Models\LogStok::create([
                'owner_id' => $ownerId,
                'pos_produk_id' => $produk->id,
                'pos_toko_id' => $stokEntry->pos_toko_id,
                'stok_sebelum' => $stokEntry->stok,
                'stok_sesudah' => max(0, $stokEntry->stok - 1),
                'perubahan' => -1,
                'tipe' => 'keluar',
                'referensi' => 'Hapus Produk',
                'keterangan' => 'Produk dihapus: ' . $produk->nama,
                'pos_pengguna_id' => $user->id,
            ]);
            
            // Reduce stock count by 1
            $newStok = max(0, $stokEntry->stok - 1);
            
            // Check if current stok entry references the product being deleted
            $needsNewRepresentative = ($stokEntry->pos_produk_id == $produk->id);
            
            if ($needsNewRepresentative && $newRepresentativeProduk) {
                // Update to new representative and reduce stock
                $stokEntry->update([
                    'stok' => $newStok,
                    'pos_produk_id' => $newRepresentativeProduk->id
                ]);
            } else {
                // Just reduce stock, save merk_name snapshot if no representative left
                $updateData = ['stok' => $newStok];
                if ($needsNewRepresentative && !$newRepresentativeProduk) {
                    $updateData['merk_name'] = $produk->merk ? $produk->merk->nama : $produk->nama;
                }
                $stokEntry->update($updateData);
            }
        }

        // Delete related records
        // 1. Delete biaya tambahan
        DB::table('pos_produk_biaya_tambahan')->where('pos_produk_id', $produk->id)->delete();
        
        // 2. Delete log stok for this specific product
        \App\Models\LogStok::where('pos_produk_id', $produk->id)->delete();
        
        // 3. Delete the product
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Bulk delete products
     * Deletes individual products and REDUCES stock counts (does not delete entire stock entries)
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('produk.delete')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus produk.');
        }

        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu produk untuk dihapus');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get products to be deleted with their merk info
        $products = PosProduk::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->with('merk')
            ->get();

        // Group products by merk_id to efficiently reduce stock
        $productsByMerk = [];
        foreach ($products as $prod) {
            $merkId = $prod->pos_produk_merk_id;
            if (!isset($productsByMerk[$merkId])) {
                $productsByMerk[$merkId] = [];
            }
            $productsByMerk[$merkId][] = $prod;
        }

        // Reduce stock for each merk group
        foreach ($productsByMerk as $merkId => $merkProducts) {
            $countToReduce = count($merkProducts);
            $productIdsBeingDeleted = array_map(fn($p) => $p->id, $merkProducts);
            
            // Find a remaining product with same merk to use as new representative (exclude products being deleted)
            $newRepresentativeProduk = PosProduk::where('owner_id', $ownerId)
                ->where('pos_produk_merk_id', $merkId)
                ->whereNotIn('id', $productIdsBeingDeleted) // Exclude products being deleted
                ->orderBy('id', 'asc') // Use oldest remaining product
                ->first();
            
            // Find ALL stock entries for this MERK (not just those referencing deleted products)
            $stokEntriesForMerk = \App\Models\ProdukStok::where('owner_id', $ownerId)
                ->whereHas('produk', function($query) use ($merkId) {
                    $query->where('pos_produk_merk_id', $merkId);
                })
                ->get();

            foreach ($stokEntriesForMerk as $stokEntry) {
                // Create log stok for stock reduction
                \App\Models\LogStok::create([
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $stokEntry->pos_produk_id, // Use current reference
                    'pos_toko_id' => $stokEntry->pos_toko_id,
                    'stok_sebelum' => $stokEntry->stok,
                    'stok_sesudah' => max(0, $stokEntry->stok - $countToReduce),
                    'perubahan' => -$countToReduce,
                    'tipe' => 'keluar',
                    'referensi' => 'Hapus Produk Massal',
                    'keterangan' => 'Bulk delete ' . $countToReduce . ' produk dengan merk yang sama',
                    'pos_pengguna_id' => $user->id,
                ]);
                
                // Reduce stock count
                $newStok = max(0, $stokEntry->stok - $countToReduce);
                
                // Check if current stok entry references any product being deleted
                $needsNewRepresentative = in_array($stokEntry->pos_produk_id, $productIdsBeingDeleted);
                
                if ($needsNewRepresentative && $newRepresentativeProduk) {
                    // Update to new representative and reduce stock
                    $stokEntry->update([
                        'stok' => $newStok,
                        'pos_produk_id' => $newRepresentativeProduk->id
                    ]);
                } else {
                    // Just reduce stock, save merk_name snapshot if no representative left
                    $updateData = ['stok' => $newStok];
                    if ($needsNewRepresentative && !$newRepresentativeProduk) {
                        $firstProd = $merkProducts[0];
                        $updateData['merk_name'] = $firstProd->merk ? $firstProd->merk->nama : $firstProd->nama;
                    }
                    $stokEntry->update($updateData);
                }
            }
        }

        // Delete related records first
        // 1. Delete biaya tambahan for all products
        DB::table('pos_produk_biaya_tambahan')->whereIn('pos_produk_id', $ids)->delete();
        
        // 2. Delete log stok for all products  
        \App\Models\LogStok::whereIn('pos_produk_id', $ids)->delete();
        
        // 3. Delete the products
        $deletedCount = PosProduk::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('produk.index')
            ->with('success', $deletedCount . ' produk berhasil dihapus');
    }

    /**
     * Quick store product from AJAX modal (for transaction forms)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickStore(Request $request)
    {
        try {
            $user = Auth::user();
            $ownerId = $user->owner ? $user->owner->id : null;

            \Log::info('quickStore request data:', $request->all());

            $productType = $request->input('product_type');

            // ============ SERVICE TYPE ============
            if ($productType === 'service') {
                $request->validate([
                    'product_type' => 'required|in:service',
                    'nama' => 'required|string|max:255',
                    'harga_beli' => 'required|numeric|min:0',
                    'harga_jual' => 'required|numeric|min:0',
                    'service_name' => 'nullable|string|max:255',
                    'service_duration' => 'nullable|integer|min:0',
                    'service_period' => 'nullable|string|in:days,weeks,months,years',
                    'deskripsi' => 'nullable|string|max:1000',
                ]);

                // Create PosService record
                $service = PosService::create([
                    'owner_id' => $ownerId,
                    'nama' => $request->nama,
                    'harga' => $request->harga_jual,
                    'keterangan' => $request->deskripsi,
                    'durasi' => $request->service_duration,
                ]);

                \Log::info('quickStore - Service created:', ['id' => $service->id, 'nama' => $service->nama]);

                return response()->json([
                    'success' => true,
                    'message' => 'Service created successfully',
                    'data' => [
                        'id' => $service->id,
                        'nama' => $service->nama,
                        'merk_nama' => '',
                        'harga_beli' => $request->harga_beli,
                        'harga_jual' => $service->harga,
                        'product_type' => 'service',
                        'service_duration' => $request->service_duration,
                        'service_period' => $request->service_period,
                        'service_description' => $service->keterangan,
                    ]
                ]);
            }

            // ============ ELECTRONIC / ACCESSORIES TYPE ============
            $request->validate([
                'nama' => 'nullable|string|max:255',
                'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
                'product_type' => 'required|in:electronic,accessories',
                'harga_beli' => 'required|numeric|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'warna' => 'nullable|string|max:255',
                'ram' => 'nullable|string|max:255',
                'penyimpanan' => 'nullable|string|max:255',
                'battery_health' => 'nullable|numeric|min:0|max:100',
                'imei' => 'nullable|string|max:255',
                'biaya_tambahan' => 'nullable|array',
                'biaya_tambahan.*.nama' => 'required_with:biaya_tambahan|string|max:255',
                'biaya_tambahan.*.harga' => 'required_with:biaya_tambahan|numeric|min:0',
            ]);

            \Log::info('After validation - warna: ' . $request->warna . ', ram: ' . $request->ram . ', penyimpanan: ' . $request->penyimpanan);

            // Calculate total biaya tambahan
            $biayaTambahanData = $request->input('biaya_tambahan', []);
            $totalBiayaTambahan = 0;
            if (!empty($biayaTambahanData) && is_array($biayaTambahanData)) {
                foreach ($biayaTambahanData as $item) {
                    if (isset($item['harga']) && $item['harga'] > 0) {
                        $totalBiayaTambahan += $item['harga'];
                    }
                }
            }

            // Calculate final purchase price (base + add-on costs)
            $basePurchasePrice = $request->harga_beli;
            $finalPurchasePrice = $basePurchasePrice + $totalBiayaTambahan;

            \Log::info('Price calculation:', [
                'base_purchase_price' => $basePurchasePrice,
                'total_biaya_tambahan' => $totalBiayaTambahan,
                'final_purchase_price' => $finalPurchasePrice,
            ]);

            // Auto-generate nama dari tipe saja jika kosong
            $nama = $request->nama;
            if (empty($nama)) {
                $merk = PosProdukMerk::find($request->pos_produk_merk_id);
                $nama = ($merk && $merk->nama) ? $merk->nama : 'Produk Baru';
            }

            // Check if product with same characteristics already exists
            // This prevents duplicate products
            // IMPORTANT: For electronic products with IMEI, IMEI must be unique (each unit is different)
            $existingProduk = PosProduk::where('owner_id', $ownerId)
                ->where('pos_produk_merk_id', $request->pos_produk_merk_id)
                ->where('nama', $nama)
                ->where('product_type', $request->product_type)
                ->where('pos_warna_id', !empty($request->warna) ? $request->warna : null)
                ->where('pos_ram_id', !empty($request->ram) ? $request->ram : null)
                ->where('pos_penyimpanan_id', !empty($request->penyimpanan) ? $request->penyimpanan : null)
                ->where('imei', !empty($request->imei) ? $request->imei : null) // IMEI must match for uniqueness
                ->first();

            if ($existingProduk) {
                \Log::info('quickStore - Product already exists:', [
                    'id' => $existingProduk->id,
                    'nama' => $existingProduk->nama,
                    'merk_id' => $existingProduk->pos_produk_merk_id,
                ]);
                
                $produk = $existingProduk;
            } else {
                // Create product if it doesn't exist
                $createData = [
                    'owner_id' => $ownerId,
                    'pos_produk_merk_id' => $request->pos_produk_merk_id,
                    'nama' => $nama,
                    'product_type' => $request->product_type,
                    'harga_beli' => $basePurchasePrice, // Only base purchase price (biaya_tambahan stored separately)
                    'harga_jual' => $request->harga_jual,
                    'pos_warna_id' => !empty($request->warna) ? $request->warna : null,
                    'pos_ram_id' => !empty($request->ram) ? $request->ram : null,
                    'pos_penyimpanan_id' => !empty($request->penyimpanan) ? $request->penyimpanan : null,
                    'battery_health' => !empty($request->battery_health) ? $request->battery_health : null,
                    'imei' => $request->imei,
                ];
                
                \Log::info('Data to create product:', $createData);
                
                $produk = PosProduk::create($createData);

                // DO NOT create stock entries here for quick add from transactions
                // Stock will be created when transaction is marked as "completed"
                // This prevents double stock creation (once here, once in storeKeluar)
                
                \Log::info('quickStore - Product created (stock will be added on transaction completion):', [
                    'produk_id' => $produk->id,
                    'note' => 'Stock entries NOT created yet - waiting for transaction completion'
                ]);
            }

            // Create or update color (warna)
            if (!empty($request->warna)) {
                \App\Models\PosWarna::firstOrCreate(
                    ['id' => $request->warna],
                    ['id_owner' => $ownerId, 'is_global' => 0]
                );
            }

            // Handle RAM - ensure it exists
            if (!empty($request->ram)) {
                \App\Models\PosRam::firstOrCreate(
                    ['id' => $request->ram],
                    ['id_owner' => $ownerId, 'is_global' => 0]
                );
            }

            // Handle Storage (Penyimpanan)
            if (!empty($request->penyimpanan)) {
                \App\Models\PosPenyimpanan::firstOrCreate(
                    ['id' => $request->penyimpanan],
                    ['id_owner' => $ownerId]
                );
            }

            // Handle Biaya Tambahan (Add On costs) - Only for electronic products
            $biayaTambahanData = $request->input('biaya_tambahan', []);
            
            \Log::info('=== QUICKSTORE BIAYA TAMBAHAN DEBUG START ===', [
                'produk_id' => $produk->id,
                'product_type' => $request->product_type,
                'biaya_tambahan_data' => $biayaTambahanData,
                'is_empty' => empty($biayaTambahanData),
                'is_electronic' => $request->product_type === 'electronic',
                'existing_produk' => $existingProduk ? 'YES' : 'NO',
                'will_process' => (!empty($biayaTambahanData) && $request->product_type === 'electronic' && !$existingProduk),
            ]);
            
            if (!empty($biayaTambahanData) && $request->product_type === 'electronic' && !$existingProduk) {
                $savedCount = 0;
                foreach ($biayaTambahanData as $index => $item) {
                    \Log::info("Processing biaya tambahan item #{$index}:", [
                        'item' => $item,
                        'nama_empty' => empty($item['nama']),
                        'harga_isset' => isset($item['harga']),
                        'harga_value' => $item['harga'] ?? 'not set',
                        'harga_gt_0' => isset($item['harga']) && $item['harga'] > 0,
                    ]);
                    
                    if (!empty($item['nama']) && isset($item['harga']) && $item['harga'] > 0) {
                        try {
                            // Insert with timestamps
                            $inserted = DB::table('pos_produk_biaya_tambahan')->insert([
                                'pos_produk_id' => $produk->id,
                                'nama' => $item['nama'],
                                'harga' => $item['harga'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            if ($inserted) {
                                $savedCount++;
                                \Log::info('✅ Biaya tambahan inserted successfully:', [
                                    'produk_id' => $produk->id,
                                    'nama' => $item['nama'],
                                    'harga' => $item['harga'],
                                ]);
                            } else {
                                \Log::error('❌ Insert returned false', [
                                    'produk_id' => $produk->id,
                                    'item' => $item,
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('❌ Failed to insert biaya tambahan (EXCEPTION):', [
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                                'produk_id' => $produk->id,
                                'nama' => $item['nama'],
                                'harga' => $item['harga'],
                            ]);
                        }
                    } else {
                        \Log::warning('⚠️ Skipping biaya tambahan item (validation failed):', [
                            'item' => $item,
                        ]);
                    }
                }
                
                \Log::info('=== QUICKSTORE BIAYA TAMBAHAN DEBUG END ===', [
                    'produk_id' => $produk->id,
                    'total_items' => count($biayaTambahanData),
                    'saved_count' => $savedCount,
                ]);
                
                // Verify what was saved
                $verify = DB::table('pos_produk_biaya_tambahan')
                    ->where('pos_produk_id', $produk->id)
                    ->get();
                \Log::info('Verification query result:', [
                    'produk_id' => $produk->id,
                    'count' => $verify->count(),
                    'data' => $verify->toArray(),
                ]);
            } else {
                \Log::warning('⚠️ Biaya tambahan NOT processed (conditions not met)');
            }

            // Load relationship for response
            $produk->load('merk');

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'merk_nama' => $produk->merk->nama ?? '',
                    'harga_beli' => $produk->harga_beli,
                    'harga_jual' => $produk->harga_jual,
                    'ram' => $produk->pos_ram_id,
                    'penyimpanan' => $produk->pos_penyimpanan_id,
                    'battery_health' => $produk->battery_health,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }
}

