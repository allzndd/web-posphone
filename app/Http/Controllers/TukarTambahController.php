<?php

namespace App\Http\Controllers;

use App\Models\PosTukarTambah;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use App\Traits\UpdatesStock;
use Illuminate\Http\Request;

class TukarTambahController extends Controller
{
    use UpdatesStock;
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

        $tukarTambah = PosTukarTambah::create($validated);

        // Update stock for trade-in
        // Product IN (from customer) - increase stock
        $this->updateProductStock(
            $validated['owner_id'],
            $validated['pos_toko_id'],
            $validated['pos_produk_masuk_id'],
            1,
            'masuk',
            'Trade-In #' . $tukarTambah->id,
            'Produk masuk dari trade-in'
        );

        // Product OUT (to customer) - decrease stock
        $this->updateProductStock(
            $validated['owner_id'],
            $validated['pos_toko_id'],
            $validated['pos_produk_keluar_id'],
            -1,
            'keluar',
            'Trade-In #' . $tukarTambah->id,
            'Produk keluar untuk trade-in'
        );

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

        // Revert old stock changes
        // Old product IN - decrease stock
        $this->updateProductStock(
            $tukarTambah->owner_id,
            $tukarTambah->pos_toko_id,
            $tukarTambah->pos_produk_masuk_id,
            -1,
            'adjustment',
            'Trade-In #' . $tukarTambah->id . ' (Update)',
            'Koreksi produk masuk trade-in yang diupdate'
        );

        // Old product OUT - increase stock
        $this->updateProductStock(
            $tukarTambah->owner_id,
            $tukarTambah->pos_toko_id,
            $tukarTambah->pos_produk_keluar_id,
            1,
            'adjustment',
            'Trade-In #' . $tukarTambah->id . ' (Update)',
            'Koreksi produk keluar trade-in yang diupdate'
        );

        // Update the trade-in record
        $tukarTambah->update($validated);

        // Apply new stock changes
        // New product IN - increase stock
        $this->updateProductStock(
            $validated['owner_id'] ?? $tukarTambah->owner_id,
            $validated['pos_toko_id'],
            $validated['pos_produk_masuk_id'],
            1,
            'masuk',
            'Trade-In #' . $tukarTambah->id . ' (Updated)',
            'Produk masuk dari trade-in (updated)'
        );

        // New product OUT - decrease stock
        $this->updateProductStock(
            $validated['owner_id'] ?? $tukarTambah->owner_id,
            $validated['pos_toko_id'],
            $validated['pos_produk_keluar_id'],
            -1,
            'keluar',
            'Trade-In #' . $tukarTambah->id . ' (Updated)',
            'Produk keluar untuk trade-in (updated)'
        );

        return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil diupdate');
    }

    public function destroy(PosTukarTambah $tukarTambah)
    {
        $tukarTambah->delete();

        return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil dihapus');
    }
}
