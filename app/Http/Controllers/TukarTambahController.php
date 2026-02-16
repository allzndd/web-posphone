<?php

namespace App\Http\Controllers;

use App\Models\PosTukarTambah;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use App\Models\PosProdukMerk;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Traits\UpdatesStock;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TukarTambahController extends Controller
{
    use UpdatesStock;
    
    public function index(Request $request)
    {
        // Check permission read
        $hasAccessRead = PermissionService::check('tukar-tambah.read');
        
        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $query = PosTukarTambah::with([
            'toko', 
            'pelanggan', 
            'produkMasuk', 
            'produkKeluar',
            'transaksiPenjualan',
            'transaksiPembelian'
        ])->where('owner_id', $ownerId);

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

        // Pass permission flags to view
        $canCreate = PermissionService::check('tukar-tambah.create');
        $canUpdate = PermissionService::check('tukar-tambah.update');
        $canDelete = PermissionService::check('tukar-tambah.delete');
        $hasActions = $canUpdate || $canDelete;

        return view('pages.tukar-tambah.index', compact('tukarTambahs', 'tokos', 'pelanggans', 'canCreate', 'canUpdate', 'canDelete', 'hasActions', 'hasAccessRead'));
    }

    public function create()
    {
        // Check permission create
        if (!PermissionService::check('tukar-tambah.create')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk membuat trade-in baru');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->get();
        $merks = PosProdukMerk::where('owner_id', $ownerId)->get();

        return view('pages.tukar-tambah.create', compact('tokos', 'pelanggans', 'produks', 'merks'));
    }

    public function store(Request $request)
    {
        // Check permission create
        if (!PermissionService::check('tukar-tambah.create')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk membuat trade-in baru');
        }

        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            
            // Produk Keluar (Penjualan) - existing product
            'pos_produk_keluar_id' => 'required|exists:pos_produk,id',
            'harga_jual_keluar' => 'required|numeric|min:0',
            'diskon_keluar' => 'nullable|numeric|min:0',
            
            // Produk Masuk (Pembelian) - could be new or existing
            'produk_masuk_type' => 'required|in:existing,new',
            'pos_produk_masuk_id' => 'required_if:produk_masuk_type,existing|nullable|exists:pos_produk,id',
            
            // New product fields (only when produk_masuk_type is 'new')
            'merk_type' => 'nullable|in:existing,new',
            'pos_produk_merk_id' => 'nullable|exists:pos_produk_merk,id',
            'merk_nama_baru' => 'nullable|string|max:255',
            'produk_nama_baru' => 'required_if:produk_masuk_type,new|nullable|string|max:255',
            'warna' => 'nullable|string|max:255',
            'penyimpanan' => 'nullable|string|max:255',
            'battery_health' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:255',
            'harga_beli_masuk' => 'required|numeric|min:0',
            
            // Transaction details
            'metode_pembayaran' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Additional validation for new product
        if ($request->produk_masuk_type === 'new') {
            if ($request->merk_type === 'existing' && !$request->pos_produk_merk_id) {
                return back()->withErrors(['pos_produk_merk_id' => 'Please select a brand.'])->withInput();
            }
            if ($request->merk_type === 'new' && !$request->merk_nama_baru) {
                return back()->withErrors(['merk_nama_baru' => 'Please enter a brand name.'])->withInput();
            }
            if (!$request->merk_type) {
                return back()->withErrors(['merk_type' => 'Please select brand type.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            // Handle new product incoming (if new)
            if ($validated['produk_masuk_type'] === 'new') {
                // Handle new merk if needed
                if ($validated['merk_type'] === 'new') {
                    $merk = PosProdukMerk::create([
                        'owner_id' => $ownerId,
                        'nama' => $validated['merk_nama_baru'],
                        'slug' => Str::slug($validated['merk_nama_baru']),
                    ]);
                    $validated['pos_produk_merk_id'] = $merk->id;
                }

                // Create new product
                $produkMasuk = PosProduk::create([
                    'owner_id' => $ownerId,
                    'pos_produk_merk_id' => $validated['pos_produk_merk_id'],
                    'nama' => $validated['produk_nama_baru'],
                    'slug' => Str::slug($validated['produk_nama_baru']),
                    'warna' => $validated['warna'] ?? null,
                    'penyimpanan' => $validated['penyimpanan'] ?? null,
                    'battery_health' => $validated['battery_health'] ?? null,
                    'imei' => $validated['imei'] ?? null,
                    'aksesoris' => $validated['aksesoris'] ?? null,
                    'harga_beli' => $validated['harga_beli_masuk'],
                    'harga_jual' => $validated['harga_beli_masuk'] * 1.2, // Default margin 20%
                ]);
                $validated['pos_produk_masuk_id'] = $produkMasuk->id;
            }

            // Create trade-in record
            $tukarTambah = PosTukarTambah::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_pelanggan_id' => $validated['pos_pelanggan_id'],
                'pos_produk_masuk_id' => $validated['pos_produk_masuk_id'],
                'pos_produk_keluar_id' => $validated['pos_produk_keluar_id'],
            ]);

            // Generate invoice numbers
            $invoicePenjualan = 'TT-OUT-' . str_pad($tukarTambah->id, 6, '0', STR_PAD_LEFT);
            $invoicePembelian = 'TT-IN-' . str_pad($tukarTambah->id, 6, '0', STR_PAD_LEFT);

            // 1. Create PENJUALAN transaction (produk keluar - income)
            $produkKeluar = PosProduk::find($validated['pos_produk_keluar_id']);
            $diskonKeluar = $validated['diskon_keluar'] ?? 0;
            $subtotalKeluar = $validated['harga_jual_keluar'] - $diskonKeluar;
            
            $transaksiPenjualan = PosTransaksi::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_pelanggan_id' => $validated['pos_pelanggan_id'],
                'pos_supplier_id' => null,
                'is_transaksi_masuk' => 1, // Income
                'invoice' => $invoicePenjualan,
                'total_harga' => $subtotalKeluar,
                'keterangan' => 'Penjualan Trade-In: ' . ($validated['keterangan'] ?? ''),
                'status' => 'completed',
                'metode_pembayaran' => strtolower($validated['metode_pembayaran']),
                'pos_tukar_tambah_id' => $tukarTambah->id,
            ]);

            // Create transaction item for penjualan
            PosTransaksiItem::create([
                'pos_transaksi_id' => $transaksiPenjualan->id,
                'pos_produk_id' => $validated['pos_produk_keluar_id'],
                'pos_service_id' => null,
                'quantity' => 1,
                'harga_satuan' => $validated['harga_jual_keluar'],
                'subtotal' => $subtotalKeluar,
                'diskon' => $diskonKeluar,
                'garansi' => 0,
                'garansi_expires_at' => null,
                'pajak' => 0,
            ]);

            // Update stock - produk keluar (decrease)
            $this->updateProductStock(
                $ownerId,
                $validated['pos_toko_id'],
                $validated['pos_produk_keluar_id'],
                -1,
                'keluar',
                $invoicePenjualan,
                'Penjualan produk keluar trade-in'
            );

            // 2. Create PEMBELIAN transaction (produk masuk - expense)
            $transaksiPembelian = PosTransaksi::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_pelanggan_id' => null,
                'pos_supplier_id' => $validated['pos_pelanggan_id'], // Customer acts as supplier
                'is_transaksi_masuk' => 0, // Expense
                'invoice' => $invoicePembelian,
                'total_harga' => $validated['harga_beli_masuk'],
                'keterangan' => 'Pembelian Trade-In: ' . ($validated['keterangan'] ?? ''),
                'status' => 'completed',
                'metode_pembayaran' => strtolower($validated['metode_pembayaran']),
                'pos_tukar_tambah_id' => $tukarTambah->id,
            ]);

            // Create transaction item for pembelian
            PosTransaksiItem::create([
                'pos_transaksi_id' => $transaksiPembelian->id,
                'pos_produk_id' => $validated['pos_produk_masuk_id'],
                'pos_service_id' => null,
                'quantity' => 1,
                'harga_satuan' => $validated['harga_beli_masuk'],
                'subtotal' => $validated['harga_beli_masuk'],
                'diskon' => 0,
                'garansi' => 0,
                'garansi_expires_at' => null,
                'pajak' => 0,
            ]);

            // Update stock - produk masuk (increase)
            $this->updateProductStock(
                $ownerId,
                $validated['pos_toko_id'],
                $validated['pos_produk_masuk_id'],
                1,
                'masuk',
                $invoicePembelian,
                'Pembelian produk masuk trade-in'
            );

            DB::commit();
            return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil ditambahkan dengan 2 transaksi terkait');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan trade-in: ' . $e->getMessage()]);
        }
    }

    public function edit(PosTukarTambah $tukarTambah)
    {
        // Check permission update
        if (!PermissionService::check('tukar-tambah.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah trade-in');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->get();
        $merks = PosProdukMerk::where('owner_id', $ownerId)->get();
        
        // Load transaksi terkait
        $tukarTambah->load(['transaksiPenjualan.items', 'transaksiPembelian.items']);

        return view('pages.tukar-tambah.edit', compact('tukarTambah', 'tokos', 'pelanggans', 'produks', 'merks'));
    }

    public function update(Request $request, PosTukarTambah $tukarTambah)
    {
        // Check permission update
        if (!PermissionService::check('tukar-tambah.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah trade-in');
        }

        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'pos_produk_keluar_id' => 'required|exists:pos_produk,id',
            'pos_produk_masuk_id' => 'required|exists:pos_produk,id',
            'harga_jual_keluar' => 'required|numeric|min:0',
            'diskon_keluar' => 'nullable|numeric|min:0',
            'harga_beli_masuk' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Revert old stock changes
            if ($tukarTambah->pos_produk_keluar_id) {
                $this->updateProductStock(
                    $tukarTambah->owner_id,
                    $tukarTambah->pos_toko_id,
                    $tukarTambah->pos_produk_keluar_id,
                    1, // Return stock
                    'adjustment',
                    'Koreksi Trade-In #' . $tukarTambah->id,
                    'Koreksi produk keluar trade-in yang diupdate'
                );
            }

            if ($tukarTambah->pos_produk_masuk_id) {
                $this->updateProductStock(
                    $tukarTambah->owner_id,
                    $tukarTambah->pos_toko_id,
                    $tukarTambah->pos_produk_masuk_id,
                    -1, // Remove stock
                    'adjustment',
                    'Koreksi Trade-In #' . $tukarTambah->id,
                    'Koreksi produk masuk trade-in yang diupdate'
                );
            }

            // Update trade-in record
            $tukarTambah->update([
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_pelanggan_id' => $validated['pos_pelanggan_id'],
                'pos_produk_masuk_id' => $validated['pos_produk_masuk_id'],
                'pos_produk_keluar_id' => $validated['pos_produk_keluar_id'],
            ]);

            // Update or create transactions
            $diskonKeluar = $validated['diskon_keluar'] ?? 0;
            $subtotalKeluar = $validated['harga_jual_keluar'] - $diskonKeluar;

            // Update penjualan transaction
            $transaksiPenjualan = $tukarTambah->transaksiPenjualan;
            if ($transaksiPenjualan) {
                $transaksiPenjualan->update([
                    'pos_toko_id' => $validated['pos_toko_id'],
                    'pos_pelanggan_id' => $validated['pos_pelanggan_id'],
                    'total_harga' => $subtotalKeluar,
                    'keterangan' => 'Penjualan Trade-In (Updated): ' . ($validated['keterangan'] ?? ''),
                    'metode_pembayaran' => strtolower($validated['metode_pembayaran']),
                ]);

                // Update item
                $item = $transaksiPenjualan->items()->first();
                if ($item) {
                    $item->update([
                        'pos_produk_id' => $validated['pos_produk_keluar_id'],
                        'harga_satuan' => $validated['harga_jual_keluar'],
                        'subtotal' => $subtotalKeluar,
                        'diskon' => $diskonKeluar,
                    ]);
                }
            }

            // Update pembelian transaction
            $transaksiPembelian = $tukarTambah->transaksiPembelian;
            if ($transaksiPembelian) {
                $transaksiPembelian->update([
                    'pos_toko_id' => $validated['pos_toko_id'],
                    'pos_supplier_id' => $validated['pos_pelanggan_id'],
                    'total_harga' => $validated['harga_beli_masuk'],
                    'keterangan' => 'Pembelian Trade-In (Updated): ' . ($validated['keterangan'] ?? ''),
                    'metode_pembayaran' => strtolower($validated['metode_pembayaran']),
                ]);

                // Update item
                $item = $transaksiPembelian->items()->first();
                if ($item) {
                    $item->update([
                        'pos_produk_id' => $validated['pos_produk_masuk_id'],
                        'harga_satuan' => $validated['harga_beli_masuk'],
                        'subtotal' => $validated['harga_beli_masuk'],
                    ]);
                }
            }

            // Apply new stock changes
            $this->updateProductStock(
                $tukarTambah->owner_id,
                $validated['pos_toko_id'],
                $validated['pos_produk_keluar_id'],
                -1,
                'keluar',
                'Trade-In #' . $tukarTambah->id . ' (Updated)',
                'Produk keluar trade-in (updated)'
            );

            $this->updateProductStock(
                $tukarTambah->owner_id,
                $validated['pos_toko_id'],
                $validated['pos_produk_masuk_id'],
                1,
                'masuk',
                'Trade-In #' . $tukarTambah->id . ' (Updated)',
                'Produk masuk trade-in (updated)'
            );

            DB::commit();
            return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil diupdate');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal mengupdate trade-in: ' . $e->getMessage()]);
        }
    }

    public function destroy(PosTukarTambah $tukarTambah)
    {
        // Check permission delete
        if (!PermissionService::check('tukar-tambah.delete')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk menghapus trade-in');
        }

        DB::beginTransaction();
        try {
            // Delete related transactions and their items
            foreach ($tukarTambah->transaksi as $transaksi) {
                $transaksi->items()->delete();
                $transaksi->delete();
            }

            // Revert stock changes
            if ($tukarTambah->pos_produk_keluar_id) {
                $this->updateProductStock(
                    $tukarTambah->owner_id,
                    $tukarTambah->pos_toko_id,
                    $tukarTambah->pos_produk_keluar_id,
                    1,
                    'adjustment',
                    'Hapus Trade-In #' . $tukarTambah->id,
                    'Return stock dari trade-in yang dihapus'
                );
            }

            if ($tukarTambah->pos_produk_masuk_id) {
                $this->updateProductStock(
                    $tukarTambah->owner_id,
                    $tukarTambah->pos_toko_id,
                    $tukarTambah->pos_produk_masuk_id,
                    -1,
                    'adjustment',
                    'Hapus Trade-In #' . $tukarTambah->id,
                    'Remove stock dari trade-in yang dihapus'
                );
            }

            $tukarTambah->delete();

            DB::commit();
            return redirect()->route('tukar-tambah.index')->with('success', 'Trade-in berhasil dihapus');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus trade-in: ' . $e->getMessage()]);
        }
    }
}
