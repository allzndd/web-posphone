<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogStok;
use App\Models\PosProduk;
use App\Models\PosToko;
use App\Models\ProdukStok;
use Illuminate\Http\Request;

class NewProductController extends Controller
{
    /**
     * Store a newly created product via API (mirrors web "New Product" form).
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner->id ?? null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $validated = $request->validate([
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
                'stok_awal' => 'nullable|integer|min:0',
            ]);

            $biayaTambahan = null;
            if ($request->has('cost_names') && $request->has('cost_amounts')) {
                $names = array_filter($request->input('cost_names', []), fn ($value) => $value !== null && $value !== '');
                $amounts = array_filter($request->input('cost_amounts', []), fn ($value) => $value !== null && $value !== '');

                if (!empty($names) && !empty($amounts) && count($names) === count($amounts)) {
                    $biayaTambahan = array_combine(array_values($names), array_values($amounts));
                }
            }

            $stokAwal = $validated['stok_awal'] ?? 1;

            $product = PosProduk::create([
                'owner_id' => $ownerId,
                'pos_produk_merk_id' => $validated['pos_produk_merk_id'],
                'product_type' => $validated['product_type'],
                'nama' => $validated['nama'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'warna' => $validated['warna'] ?? null,
                'penyimpanan' => $validated['penyimpanan'] ?? null,
                'battery_health' => $validated['battery_health'] ?? null,
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'biaya_tambahan' => $biayaTambahan,
                'imei' => $validated['imei'],
                'aksesoris' => $validated['aksesoris'] ?? null,
            ]);

            $stores = PosToko::where('owner_id', $ownerId)->get();
            foreach ($stores as $store) {
                ProdukStok::create([
                    'owner_id' => $ownerId,
                    'pos_toko_id' => $store->id,
                    'pos_produk_id' => $product->id,
                    'stok' => $stokAwal,
                ]);

                LogStok::create([
                    'owner_id' => $ownerId,
                    'pos_produk_id' => $product->id,
                    'pos_toko_id' => $store->id,
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $stokAwal,
                    'perubahan' => $stokAwal,
                    'tipe' => 'masuk',
                    'referensi' => 'Produk Baru: ' . ($product->nama ?? 'Tanpa Nama'),
                    'keterangan' => 'Stok awal produk baru',
                    'pos_pengguna_id' => $user->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dibuat',
                'data' => $product->load(['merk', 'stok.toko']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}
