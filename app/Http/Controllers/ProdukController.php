<?php

namespace App\Http\Controllers;

use App\Models\PosProduk;
use App\Models\PosProdukMerk;
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
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $produk = PosProduk::where('owner_id', $ownerId)
            ->with(['merk'])
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.produk.index', compact('produk'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
            'imei' => 'required|string|max:255',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosProduk $produk)
    {
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
            'imei' => 'required|string|max:255',
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
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
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

            $request->validate([
                'nama' => 'required|string|max:255',
                'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
                'harga_beli' => 'required|numeric|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'warna' => 'nullable|string|max:255',
                'penyimpanan' => 'nullable|string|max:255',
                'battery_health' => 'nullable|string|max:255',
                'imei' => 'nullable|string|max:255',
            ]);

            $produk = PosProduk::create([
                'owner_id' => $ownerId,
                'pos_produk_merk_id' => $request->pos_produk_merk_id,
                'nama' => $request->nama,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual,
                'warna' => $request->warna,
                'penyimpanan' => $request->penyimpanan,
                'battery_health' => $request->battery_health,
                'imei' => $request->imei,
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
                    'referensi' => 'Produk Baru (Quick Add): ' . $produk->nama,
                    'keterangan' => 'Stok awal produk baru dari transaksi',
                    'pos_pengguna_id' => $user->id,
                ]);
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

