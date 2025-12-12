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
            'nama' => 'required|string|max:255',
            'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'penyimpanan' => 'nullable|string|max:255',
            'battery_health' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'biaya_tambahan' => 'nullable|string',
            'imei' => 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:45',
        ]);

        // Parse biaya_tambahan JSON if provided
        $biayaTambahan = null;
        if ($request->filled('biaya_tambahan')) {
            $biayaTambahan = json_decode($request->biaya_tambahan, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['biaya_tambahan' => 'Format JSON tidak valid'])->withInput();
            }
        }

        PosProduk::create([
            'owner_id' => $ownerId,
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'nama' => $request->nama,
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
    public function edit($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $produk = PosProduk::findOrFail($id);
        $merks = PosProdukMerk::where('owner_id', $ownerId)->get();

        return view('pages.produk.edit', compact('produk', 'merks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = PosProduk::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'pos_produk_merk_id' => 'required|exists:pos_produk_merk,id',
            'deskripsi' => 'nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'penyimpanan' => 'nullable|string|max:255',
            'battery_health' => 'nullable|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'biaya_tambahan' => 'nullable|string',
            'imei' => 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:45',
        ]);

        // Parse biaya_tambahan JSON if provided
        $biayaTambahan = null;
        if ($request->filled('biaya_tambahan')) {
            $biayaTambahan = json_decode($request->biaya_tambahan, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['biaya_tambahan' => 'Format JSON tidak valid'])->withInput();
            }
        }

        $produk->update([
            'pos_produk_merk_id' => $request->pos_produk_merk_id,
            'nama' => $request->nama,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = PosProduk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
}
