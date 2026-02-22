<?php

namespace App\Http\Controllers;

use App\Models\PosProduk;
use App\Models\PosProdukMerk;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $produk = PosProduk::where('owner_id', $ownerId)
            ->with(['merk', 'stok'])
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

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

        $produk = PosProduk::create([
            'owner_id' => $ownerId,
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'product_type' => $request->product_type,
            'nama' => $nama,
            'deskripsi' => $request->deskripsi,
            'warna' => $request->warna,
            'penyimpanan' => $request->penyimpanan,
            'battery_health' => $request->battery_health,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'biaya_tambahan' => $biayaTambahan,
            'imei' => $request->imei,
            'aksesoris' => $request->aksesoris,
        ]);

        // Automatically create stock entry for all stores with quantity 1
        $toko = \App\Models\PosToko::where('owner_id', $ownerId)->get();
        foreach ($toko as $store) {
            \App\Models\ProdukStok::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $store->id,
                'pos_produk_id' => $produk->id,
                'stok' => 1,
            ]);

            // Create log stok (stock history) for initial stock
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
        $produk->load(['merk', 'ram', 'penyimpanan', 'warna']);
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

        return view('pages.produk.edit', compact('produk', 'merks'));
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

        $produk->update([
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'product_type' => $request->product_type,
            'nama' => $nama,
            'deskripsi' => $request->deskripsi,
            'warna' => $request->warna,
            'penyimpanan' => $request->penyimpanan,
            'battery_health' => $request->battery_health,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'biaya_tambahan' => $biayaTambahan,
            'imei' => $request->imei,
            'aksesoris' => $request->aksesoris,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
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

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Bulk delete products
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
            ]);

            \Log::info('After validation - warna: ' . $request->warna . ', ram: ' . $request->ram . ', penyimpanan: ' . $request->penyimpanan);

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
                    'harga_beli' => $request->harga_beli,
                    'harga_jual' => $request->harga_jual,
                    'pos_warna_id' => !empty($request->warna) ? $request->warna : null,
                    'pos_ram_id' => !empty($request->ram) ? $request->ram : null,
                    'pos_penyimpanan_id' => !empty($request->penyimpanan) ? $request->penyimpanan : null,
                    'battery_health' => !empty($request->battery_health) ? $request->battery_health : null,
                    'imei' => $request->imei,
                ];
                
                \Log::info('Data to create product:', $createData);
                
                $produk = PosProduk::create($createData);

                // DO NOT automatically create produk_stok entries here
                // produk_stok will be created ONLY when transaction updates stock via UpdatesStock::updateProductStock
                // This prevents orphaned stok=0 entries when multiple items with same brand are grouped
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

