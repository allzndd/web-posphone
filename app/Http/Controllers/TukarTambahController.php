<?php

namespace App\Http\Controllers;

use App\Models\PosTukarTambah;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use App\Models\PosProdukMerk;
use App\Models\PosWarna;
use App\Models\PosRam;
use App\Models\PosPenyimpanan;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\ProdukStok;
use App\Traits\UpdatesStock;
use App\Services\InventoryAvailabilityService;
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

        // Stock is grouped by merk + store, not by individual product.
        $stokByMerkAndStore = ProdukStok::where('owner_id', $ownerId)
            ->with('produk')
            ->get()
            ->groupBy(function($stok) {
                return ($stok->produk ? $stok->produk->pos_produk_merk_id : 0) . '_' . $stok->pos_toko_id;
            });

        $availableProductIds = InventoryAvailabilityService::getAvailableProductIds($ownerId);

        $produks = PosProduk::where('owner_id', $ownerId)
            ->when(!empty($availableProductIds), function ($query) use ($availableProductIds) {
                return $query->whereIn('id', $availableProductIds);
            }, function ($query) {
                return $query->whereRaw('1 = 0');
            })
            ->with(['merk', 'warna', 'penyimpanan', 'ram'])
            ->get()
            ->map(function($produk) use ($stokByMerkAndStore) {
                $stokPerToko = [];

                foreach ($stokByMerkAndStore as $key => $stokEntries) {
                    $parts = explode('_', $key);
                    $merkId = (int) $parts[0];
                    $tokoId = (int) $parts[1];

                    if ($merkId === (int) $produk->pos_produk_merk_id) {
                        $stokEntry = $stokEntries->first();
                        if ($stokEntry) {
                            $stokPerToko[$tokoId] = (int) $stokEntry->stok;
                        }
                    }
                }

                $produk->stok_per_toko = $stokPerToko;

                return $produk;
            });
        
        // Get merks with their product types (nama) for brand -> type dropdown
        $merks = PosProdukMerk::where('owner_id', $ownerId)
            ->orWhereNull('owner_id') // Include global merks
            ->orderBy('merk')
            ->orderBy('nama')
            ->get();
        
        // Get master data for dropdowns (these tables use id_owner, not owner_id)
        $warnas = PosWarna::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('warna')
            ->get();
        
        $rams = PosRam::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('kapasitas')
            ->get();
        
        $penyimpanans = PosPenyimpanan::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('kapasitas')
            ->get();

        return view('pages.tukar-tambah.create', compact(
            'tokos', 
            'pelanggans', 
            'produks', 
            'merks',
            'warnas',
            'rams',
            'penyimpanans'
        ));
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
            'pos_warna_id' => 'nullable|exists:pos_warna,id',
            'pos_ram_id' => 'nullable|exists:pos_ram,id',
            'pos_penyimpanan_id' => 'nullable|exists:pos_penyimpanan,id',
            'battery_health' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'aksesoris' => 'nullable|string|max:255',
            'harga_beli_masuk' => 'required|numeric|min:0',
            'harga_jual_masuk' => 'nullable|numeric|min:0',
            'biaya_tambahan_nama' => 'nullable|array',
            'biaya_tambahan_nama.*' => 'nullable|string|max:255',
            'biaya_tambahan_nilai' => 'nullable|array',
            'biaya_tambahan_nilai.*' => 'nullable|numeric|min:0',
            
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

            // Validate outgoing product stock (grouped by merk + selected store)
            $produkKeluar = PosProduk::where('owner_id', $ownerId)
                ->with('merk')
                ->find($validated['pos_produk_keluar_id']);

            if (!$produkKeluar) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'pos_produk_keluar_id' => 'Produk keluar tidak ditemukan untuk owner ini.'
                ]);
            }

            if (!empty($produkKeluar->pos_toko_id) && (int) $produkKeluar->pos_toko_id !== (int) $validated['pos_toko_id']) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'pos_produk_keluar_id' => 'Produk keluar tidak tersedia di toko yang dipilih.'
                ]);
            }

            if (!InventoryAvailabilityService::isProductAvailableForSale(
                (int) $ownerId,
                (int) $validated['pos_toko_id'],
                (int) $validated['pos_produk_keluar_id']
            )) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'pos_produk_keluar_id' => 'Produk keluar sudah terjual atau tidak tersedia untuk dijual di toko ini.'
                ]);
            }

            $stokProdukKeluar = ProdukStok::where('owner_id', $ownerId)
                ->where('pos_toko_id', $validated['pos_toko_id'])
                ->whereHas('produk', function($query) use ($produkKeluar) {
                    $query->where('pos_produk_merk_id', $produkKeluar->pos_produk_merk_id);
                })
                ->first();

            // Fallback for orphaned stock rows (when representative product has changed/deleted)
            if (!$stokProdukKeluar && $produkKeluar->merk) {
                $merkBrand = trim((string) ($produkKeluar->merk->merk ?? ''));
                $merkType = trim((string) ($produkKeluar->merk->nama ?? ''));

                if ($merkBrand !== '' && $merkType !== '' && strtolower($merkBrand) === strtolower($merkType)) {
                    $merkLabel = $merkBrand;
                } else {
                    $merkLabel = trim($merkBrand . ' ' . $merkType);
                }

                if ($merkLabel !== '' || $merkType !== '') {
                    $stokProdukKeluar = ProdukStok::where('owner_id', $ownerId)
                        ->where('pos_toko_id', $validated['pos_toko_id'])
                        ->where(function($query) use ($merkLabel, $merkType) {
                            if ($merkLabel !== '') {
                                $query->where('merk_name', $merkLabel);

                                if ($merkType !== '' && $merkType !== $merkLabel) {
                                    $query->orWhere('merk_name', $merkType);
                                }
                            } else {
                                $query->where('merk_name', $merkType);
                            }
                        })
                        ->first();
                }
            }

            $stokTersedia = $stokProdukKeluar ? (int) $stokProdukKeluar->stok : 0;

            if ($stokTersedia < 1) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'pos_produk_keluar_id' => 'Stok produk keluar di toko terpilih habis (0). Silakan pilih produk lain.'
                ]);
            }

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

                // Build biaya_tambahan array from form data
                $biayaTambahan = [];
                if (!empty($request->biaya_tambahan_nama)) {
                    foreach ($request->biaya_tambahan_nama as $index => $nama) {
                        if (!empty($nama) && isset($request->biaya_tambahan_nilai[$index])) {
                            $biayaTambahan[] = [
                                'nama' => $nama,
                                'nilai' => (float) $request->biaya_tambahan_nilai[$index],
                            ];
                        }
                    }
                }

                // Use provided selling price, or default to purchase price
                $hargaJual = $validated['harga_jual_masuk'] ?? $validated['harga_beli_masuk'];

                // Create new product
                $produkMasuk = PosProduk::create([
                    'owner_id' => $ownerId,
                    'pos_toko_id' => $validated['pos_toko_id'],
                    'pos_produk_merk_id' => $validated['pos_produk_merk_id'],
                    'nama' => $validated['produk_nama_baru'],
                    'slug' => Str::slug($validated['produk_nama_baru']),
                    'pos_warna_id' => $validated['pos_warna_id'] ?? null,
                    'pos_ram_id' => $validated['pos_ram_id'] ?? null,
                    'pos_penyimpanan_id' => $validated['pos_penyimpanan_id'] ?? null,
                    'battery_health' => $validated['battery_health'] ?? null,
                    'imei' => $validated['imei'] ?? null,
                    'aksesoris' => $validated['aksesoris'] ?? null,
                    'harga_beli' => $validated['harga_beli_masuk'],
                    'harga_jual' => $hargaJual,
                    'biaya_tambahan' => !empty($biayaTambahan) ? $biayaTambahan : null,
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
            return redirect()->route('tukar-tambah.print', $tukarTambah->id)->with('success', 'Trade-in berhasil ditambahkan dengan 2 transaksi terkait');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan trade-in: ' . $e->getMessage()]);
        }
    }

    public function print($id)
    {
        if (!PermissionService::check('tukar-tambah.read')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk melihat nota trade-in');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tukarTambah = PosTukarTambah::where('owner_id', $ownerId)
            ->with(['toko', 'pelanggan'])
            ->findOrFail($id);

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('pos_tukar_tambah_id', $tukarTambah->id)
            ->where('is_transaksi_masuk', 1)
            ->with([
                'items.produk.merk',
                'items.produk.warna',
                'items.produk.penyimpanan',
                'items.produk.ram',
                'items.service',
                'toko',
                'pelanggan',
            ])
            ->first();

        if (!$transaksi) {
            return redirect()->route('tukar-tambah.index')->with('error', 'Nota penjualan trade-in tidak ditemukan');
        }

        return view('pages.tukar-tambah.print', compact('tukarTambah', 'transaksi'));
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

        $merks = PosProdukMerk::where('owner_id', $ownerId)
            ->orWhereNull('owner_id')
            ->orderBy('merk')
            ->orderBy('nama')
            ->get();

        $warnas = \App\Models\PosWarna::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('warna')
            ->get();

        $rams = \App\Models\PosRam::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('kapasitas')
            ->get();

        $penyimpanans = \App\Models\PosPenyimpanan::where('id_owner', $ownerId)
            ->orWhereNull('id_owner')
            ->orderBy('kapasitas')
            ->get();

        // Load transaksi & incoming product relations
        $tukarTambah->load([
            'transaksiPenjualan.items',
            'transaksiPembelian.items',
            'produkMasuk.merk',
            'produkMasuk.warna',
            'produkMasuk.ram',
            'produkMasuk.penyimpanan',
        ]);

        return view('pages.tukar-tambah.edit', compact(
            'tukarTambah', 'tokos', 'pelanggans', 'produks',
            'merks', 'warnas', 'rams', 'penyimpanans'
        ));
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
            'harga_jual_masuk' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:255',
            // Incoming product detail fields
            'pos_produk_merk_id' => 'nullable|exists:pos_produk_merk,id',
            'produk_nama_baru' => 'nullable|string|max:255',
            'pos_warna_id' => 'nullable|exists:pos_warna,id',
            'pos_ram_id' => 'nullable|exists:pos_ram,id',
            'pos_penyimpanan_id' => 'nullable|exists:pos_penyimpanan,id',
            'battery_health' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'biaya_tambahan_nama' => 'nullable|array',
            'biaya_tambahan_nama.*' => 'nullable|string|max:255',
            'biaya_tambahan_nilai' => 'nullable|array',
            'biaya_tambahan_nilai.*' => 'nullable|numeric|min:0',
        ]);

        // Lock product references after creation to avoid stock collisions.
        if ((int) $validated['pos_produk_keluar_id'] !== (int) $tukarTambah->pos_produk_keluar_id) {
            return back()->withInput()->withErrors([
                'pos_produk_keluar_id' => 'Produk keluar tidak dapat diubah setelah trade-in dibuat.'
            ]);
        }

        if ((int) $validated['pos_produk_masuk_id'] !== (int) $tukarTambah->pos_produk_masuk_id) {
            return back()->withInput()->withErrors([
                'pos_produk_masuk_id' => 'Produk masuk tidak dapat diubah setelah trade-in dibuat.'
            ]);
        }

        // Force immutable IDs (protect against request tampering).
        $validated['pos_produk_keluar_id'] = $tukarTambah->pos_produk_keluar_id;
        $validated['pos_produk_masuk_id'] = $tukarTambah->pos_produk_masuk_id;

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

            // Update incoming product (HP masuk) fields
            $produkMasuk = PosProduk::find($validated['pos_produk_masuk_id']);
            if ($produkMasuk) {
                $biayaTambahan = [];
                if (!empty($request->biaya_tambahan_nama)) {
                    foreach ($request->biaya_tambahan_nama as $index => $nama) {
                        if (!empty($nama) && isset($request->biaya_tambahan_nilai[$index])) {
                            $biayaTambahan[] = [
                                'nama' => $nama,
                                'nilai' => (float) $request->biaya_tambahan_nilai[$index],
                            ];
                        }
                    }
                }
                $produkMasuk->update([
                    'pos_toko_id'        => $validated['pos_toko_id'],
                    'pos_produk_merk_id' => $validated['pos_produk_merk_id'] ?? $produkMasuk->pos_produk_merk_id,
                    'nama'               => !empty($validated['produk_nama_baru']) ? $validated['produk_nama_baru'] : $produkMasuk->nama,
                    'slug'               => !empty($validated['produk_nama_baru']) ? Str::slug($validated['produk_nama_baru']) : $produkMasuk->slug,
                    'pos_warna_id'       => $validated['pos_warna_id'] ?? null,
                    'pos_ram_id'         => $validated['pos_ram_id'] ?? null,
                    'pos_penyimpanan_id' => $validated['pos_penyimpanan_id'] ?? null,
                    'battery_health'     => $validated['battery_health'] ?? null,
                    'imei'               => $validated['imei'] ?? null,
                    'harga_beli'         => $validated['harga_beli_masuk'],
                    'harga_jual'         => $validated['harga_jual_masuk'] ?? $validated['harga_beli_masuk'],
                    'biaya_tambahan'     => !empty($biayaTambahan) ? $biayaTambahan : null,
                ]);
            }

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
