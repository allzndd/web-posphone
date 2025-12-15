<?php

namespace App\Http\Controllers;

use App\Models\PosTukarTambah;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use Illuminate\Http\Request;

class TukarTambahController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $query = PosTukarTambah::with(['toko', 'pelanggan', 'produkMasuk', 'produkKeluar'])
            ->where('owner_id', $ownerId);

        // Filter by toko
        if ($request->filled('pos_toko_id')) {
            $query->where('pos_toko_id', $request->pos_toko_id);
        }

        // Filter by pelanggan
        if ($request->filled('pos_pelanggan_id')) {
            $query->where('pos_pelanggan_id', $request->pos_pelanggan_id);
        }

        // Search by product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('produkMasuk', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                })->orWhereHas('produkKeluar', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $perPage = $request->get('per_page', 10);
        $tukarTambahs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();

        return view('pages.tukar-tambah.index', compact('tukarTambahs', 'tokos', 'pelanggans'));
    }

    public function create()
    {
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->get();

        return view('pages.tukar-tambah.create', compact('tokos', 'pelanggans', 'produks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'pos_produk_masuk_id' => 'required|exists:pos_produk,id',
            'pos_produk_keluar_id' => 'required|exists:pos_produk,id',
        ]);

        $user = auth()->user();
        $validated['owner_id'] = $user->owner ? $user->owner->id : null;

        PosTukarTambah::create($validated);

        return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil ditambahkan');
    }

    public function edit(PosTukarTambah $tukarTambah)
    {
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->get();

        return view('pages.tukar-tambah.edit', compact('tukarTambah', 'tokos', 'pelanggans', 'produks'));
    }

    public function update(Request $request, PosTukarTambah $tukarTambah)
    {
        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'pos_produk_masuk_id' => 'required|exists:pos_produk,id',
            'pos_produk_keluar_id' => 'required|exists:pos_produk,id',
        ]);

        $tukarTambah->update($validated);

        return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil diupdate');
    }

    public function destroy(PosTukarTambah $tukarTambah)
    {
        $tukarTambah->delete();

        return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil dihapus');
    }
}
