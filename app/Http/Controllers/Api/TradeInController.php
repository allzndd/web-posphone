<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosTukarTambah;
use App\Models\PosPelanggan;
use App\Models\PosProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TradeInController extends Controller
{
    /**
     * Display a listing of trade-in transactions.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 20);
            $search = $request->input('search', '');

            $query = PosTukarTambah::with(['pelanggan', 'toko', 'produkMasuk.merk', 'produkKeluar.merk', 'transaksi']);

            // Search by customer name or product name
            if (!empty($search)) {
                $query->where(function($subQuery) use ($search) {
                    $subQuery->whereHas('pelanggan', function($custQuery) use ($search) {
                                 $custQuery->where('nama', 'like', "%{$search}%");
                             })
                             ->orWhereHas('produkMasuk', function($prodQuery) use ($search) {
                                 $prodQuery->where('nama', 'like', "%{$search}%");
                             })
                             ->orWhereHas('produkKeluar', function($prodQuery) use ($search) {
                                 $prodQuery->where('nama', 'like', "%{$search}%");
                             });
                });
            }

            $tradeIns = $query->latest()->paginate($perPage);

            // Transform data
            $data = $tradeIns->map(function ($tradeIn) {
                $produkMasuk = $tradeIn->produkMasuk;
                $produkKeluar = $tradeIn->produkKeluar;
                
                // Get transaction details
                $transaksiPenjualan = $tradeIn->transaksi->where('is_transaksi_masuk', 1)->first();
                $transaksiPembelian = $tradeIn->transaksi->where('is_transaksi_masuk', 0)->first();
                
                $hargaJualKeluar = $transaksiPenjualan ? $transaksiPenjualan->total_harga : 0;
                $hargaBeliMasuk = $transaksiPembelian ? $transaksiPembelian->total_harga : 0;
                
                return [
                    'id' => $tradeIn->id,
                    'customer_id' => $tradeIn->pos_pelanggan_id,
                    'customer_name' => $tradeIn->pelanggan->nama ?? '-',
                    'customer_phone' => $tradeIn->pelanggan->telepon ?? '-',
                    'pelanggan_nama' => $tradeIn->pelanggan->nama ?? null,
                    'toko_branch_nama' => $tradeIn->toko->nama ?? null,
                    'produk_masuk_nama' => $produkMasuk->nama ?? '-',
                    'produk_masuk_merk' => $produkMasuk->merk->nama ?? '-',
                    'produk_masuk_kondisi' => 'Good',
                    'produk_masuk_harga' => (int) $hargaBeliMasuk,
                    'produk_keluar_nama' => $produkKeluar->nama ?? '-',
                    'produk_keluar_merk' => $produkKeluar->merk->nama ?? '-',
                    'produk_keluar_harga' => (int) $hargaJualKeluar,
                    'diskon_persen' => 0,
                    'diskon_amount' => 0,
                    'net_amount' => (int) $hargaJualKeluar,
                    'selisih_harga' => (int) ($hargaJualKeluar - $hargaBeliMasuk),
                    'payment_method' => $transaksiPenjualan ? $transaksiPenjualan->metode_pembayaran : null,
                    'catatan' => $transaksiPenjualan ? $transaksiPenjualan->keterangan : null,
                    'color' => $produkMasuk->warna ?? null,
                    'storage' => $produkMasuk->penyimpanan ?? null,
                    'battery_health' => $produkMasuk->battery_health ?? null,
                    'imei' => $produkMasuk->imei ?? null,
                    'accessories' => $produkMasuk->aksesoris ?? null,
                    'difference' => (int) ($hargaJualKeluar - $hargaBeliMasuk),
                    'created_at' => $tradeIn->created_at->toIso8601String(),
                    'updated_at' => $tradeIn->updated_at->toIso8601String(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Trade-in data loaded successfully',
                'data' => $data,
                'meta' => [
                    'current_page' => $tradeIns->currentPage(),
                    'last_page' => $tradeIns->lastPage(),
                    'per_page' => $tradeIns->perPage(),
                    'total' => $tradeIns->total(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load trade-in data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created trade-in transaction.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            // Additional validation for new product
            if ($request->produk_masuk_type === 'new') {
                if ($request->merk_type === 'existing' && !$request->pos_produk_merk_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => ['pos_produk_merk_id' => ['Please select a brand.']],
                    ], 422);
                }
                if ($request->merk_type === 'new' && !$request->merk_nama_baru) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => ['merk_nama_baru' => ['Please enter a brand name.']],
                    ], 422);
                }
                if (!$request->merk_type) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => ['merk_type' => ['Please select brand type.']],
                    ], 422);
                }
            }

            \DB::beginTransaction();
            
            $user = auth()->user();
            $ownerId = $user->owner ? $user->owner->id : $user->owner_id;

            // Handle new product incoming (if new)
            if ($validated['produk_masuk_type'] === 'new') {
                // Handle new merk if needed
                if ($validated['merk_type'] === 'new') {
                    $merk = \App\Models\PosProdukMerk::create([
                        'owner_id' => $ownerId,
                        'nama' => $validated['merk_nama_baru'],
                        'slug' => \Illuminate\Support\Str::slug($validated['merk_nama_baru']),
                    ]);
                    $validated['pos_produk_merk_id'] = $merk->id;
                }

                // Create new product
                $produkMasuk = \App\Models\PosProduk::create([
                    'owner_id' => $ownerId,
                    'pos_produk_merk_id' => $validated['pos_produk_merk_id'],
                    'nama' => $validated['produk_nama_baru'],
                    'slug' => \Illuminate\Support\Str::slug($validated['produk_nama_baru']),
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
            $tradeIn = \App\Models\PosTukarTambah::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $validated['pos_toko_id'],
                'pos_pelanggan_id' => $validated['pos_pelanggan_id'],
                'pos_produk_masuk_id' => $validated['pos_produk_masuk_id'],
                'pos_produk_keluar_id' => $validated['pos_produk_keluar_id'],
            ]);

            // Generate invoice numbers
            $invoicePenjualan = 'TT-OUT-' . str_pad($tradeIn->id, 6, '0', STR_PAD_LEFT);
            $invoicePembelian = 'TT-IN-' . str_pad($tradeIn->id, 6, '0', STR_PAD_LEFT);

            // 1. Create PENJUALAN transaction (produk keluar - income)
            $produkKeluar = \App\Models\PosProduk::find($validated['pos_produk_keluar_id']);
            $diskonKeluar = $validated['diskon_keluar'] ?? 0;
            $subtotalKeluar = $validated['harga_jual_keluar'] - $diskonKeluar;
            
            $transaksiPenjualan = \App\Models\PosTransaksi::create([
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
                'pos_tukar_tambah_id' => $tradeIn->id,
            ]);

            // Create transaction item for penjualan
            \App\Models\PosTransaksiItem::create([
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

            // 2. Create PEMBELIAN transaction (produk masuk - expense)
            $transaksiPembelian = \App\Models\PosTransaksi::create([
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
                'pos_tukar_tambah_id' => $tradeIn->id,
            ]);

            // Create transaction item for pembelian
            \App\Models\PosTransaksiItem::create([
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

            \DB::commit();

            // Load relationships
            $tradeIn->load(['pelanggan', 'toko', 'produkMasuk.merk', 'produkKeluar.merk']);

            $produkMasukData = $tradeIn->produkMasuk;
            $produkKeluarData = $tradeIn->produkKeluar;

            return response()->json([
                'success' => true,
                'message' => 'Trade-in created successfully',
                'data' => [
                    'id' => $tradeIn->id,
                    'customer_id' => $tradeIn->pos_pelanggan_id,
                    'customer_name' => $tradeIn->pelanggan->nama ?? '-',
                    'pelanggan_nama' => $tradeIn->pelanggan->nama ?? null,
                    'toko_branch_nama' => $tradeIn->toko->nama ?? null,
                    'produk_masuk_nama' => $produkMasukData->nama ?? '-',
                    'produk_masuk_merk' => $produkMasukData->merk->nama ?? '-',
                    'produk_masuk_harga' => (int) $validated['harga_beli_masuk'],
                    'produk_keluar_nama' => $produkKeluarData->nama ?? '-',
                    'produk_keluar_merk' => $produkKeluarData->merk->nama ?? '-',
                    'produk_keluar_harga' => (int) $validated['harga_jual_keluar'],
                    'diskon_persen' => 0,
                    'diskon_amount' => (int) $diskonKeluar,
                    'net_amount' => (int) $subtotalKeluar,
                    'selisih_harga' => (int) ($subtotalKeluar - $validated['harga_beli_masuk']),
                    'payment_method' => $validated['metode_pembayaran'],
                    'catatan' => $validated['keterangan'] ?? null,
                    'difference' => (int) ($subtotalKeluar - $validated['harga_beli_masuk']),
                    'created_at' => $tradeIn->created_at->toIso8601String(),
                ],
            ], 201);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create trade-in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified trade-in transaction.
     */
    public function show($id)
    {
        try {
            $tradeIn = PosTukarTambah::with(['pelanggan', 'toko', 'produkMasuk.merk', 'produkKeluar.merk', 'transaksi'])->findOrFail($id);

            $produkMasuk = $tradeIn->produkMasuk;
            $produkKeluar = $tradeIn->produkKeluar;
            
            // Get transaction details
            $transaksiPenjualan = $tradeIn->transaksi->where('is_transaksi_masuk', 1)->first();
            $transaksiPembelian = $tradeIn->transaksi->where('is_transaksi_masuk', 0)->first();
            
            $hargaJualKeluar = $transaksiPenjualan ? $transaksiPenjualan->total_harga : 0;
            $hargaBeliMasuk = $transaksiPembelian ? $transaksiPembelian->total_harga : 0;

            return response()->json([
                'success' => true,
                'message' => 'Trade-in loaded successfully',
                'data' => [
                    'id' => $tradeIn->id,
                    'customer_id' => $tradeIn->pos_pelanggan_id,
                    'customer_name' => $tradeIn->pelanggan->nama ?? '-',
                    'customer_phone' => $tradeIn->pelanggan->telepon ?? '-',
                    'customer_email' => $tradeIn->pelanggan->email ?? '-',
                    'pelanggan_nama' => $tradeIn->pelanggan->nama ?? null,
                    'toko_branch_nama' => $tradeIn->toko->nama ?? null,
                    'produk_masuk_nama' => $produkMasuk->nama ?? '-',
                    'produk_masuk_merk' => $produkMasuk->merk->nama ?? '-',
                    'produk_masuk_kondisi' => 'Good',
                    'produk_masuk_harga' => (int) $hargaBeliMasuk,
                    'produk_keluar_nama' => $produkKeluar->nama ?? '-',
                    'produk_keluar_merk' => $produkKeluar->merk->nama ?? '-',
                    'produk_keluar_harga' => (int) $hargaJualKeluar,
                    'diskon_persen' => 0,
                    'diskon_amount' => 0,
                    'net_amount' => (int) $hargaJualKeluar,
                    'selisih_harga' => (int) ($hargaJualKeluar - $hargaBeliMasuk),
                    'payment_method' => $transaksiPenjualan ? $transaksiPenjualan->metode_pembayaran : null,
                    'catatan' => $transaksiPenjualan ? $transaksiPenjualan->keterangan : null,
                    'color' => $produkMasuk->warna ?? null,
                    'storage' => $produkMasuk->penyimpanan ?? null,
                    'battery_health' => $produkMasuk->battery_health ?? null,
                    'imei' => $produkMasuk->imei ?? null,
                    'accessories' => $produkMasuk->aksesoris ?? null,
                    'difference' => (int) ($hargaJualKeluar - $hargaBeliMasuk),
                    'created_at' => $tradeIn->created_at->toIso8601String(),
                    'updated_at' => $tradeIn->updated_at->toIso8601String(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trade-in not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified trade-in transaction.
     */
    public function update(Request $request, $id)
    {
        try {
            // For now, we don't support update via API
            // Trade-in should be deleted and recreated instead
            return response()->json([
                'success' => false,
                'message' => 'Update not supported. Please delete and create new trade-in.',
            ], 405);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update trade-in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified trade-in transaction.
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            
            $tradeIn = PosTukarTambah::findOrFail($id);
            
            // Delete related transactions first
            \App\Models\PosTransaksi::where('pos_tukar_tambah_id', $id)->delete();
            
            // Delete trade-in record
            $tradeIn->delete();
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trade-in deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete trade-in',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of customers for dropdown
     */
    public function getCustomers()
    {
        try {
            $customers = PosPelanggan::orderBy('nama')->get(['id', 'nama', 'telepon']);

            return response()->json([
                'success' => true,
                'data' => $customers,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load customers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of products for dropdown
     */
    public function getProducts()
    {
        try {
            $products = PosProduk::with('merk')->orderBy('nama')->get(['id', 'nama', 'imei', 'harga_jual', 'pos_produk_merk_id']);

            return response()->json([
                'success' => true,
                'data' => $products,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
