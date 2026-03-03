<?php

namespace App\Traits;

use App\Models\ProdukStok;
use App\Models\LogStok;
use App\Models\PosProduk;

trait UpdatesStock
{
    /**
     * Update product stock and create log
     * Stock is GROUPED by merk_id + store_id (not per individual product)
     * 
     * @param int $ownerId
     * @param int $tokoId
     * @param int $produkId
     * @param int $quantity (positive for incoming, negative for outgoing)
     * @param string $tipe (masuk/keluar/adjustment/retur)
     * @param string $referensi
     * @param string $keterangan
     * @return bool
     */
    protected function updateProductStock($ownerId, $tokoId, $produkId, $quantity, $tipe, $referensi, $keterangan = null)
    {
        // Get the product and its merk_id
        $product = PosProduk::find($produkId);
        if (!$product) {
            \Log::error('updateProductStock - Product not found:', ['produk_id' => $produkId]);
            return false;
        }

        $merkId = $product->pos_produk_merk_id;

        // Log input parameters
        \Log::info('updateProductStock - Input parameters:', [
            'owner_id' => $ownerId . ' (type: ' . gettype($ownerId) . ')',
            'toko_id' => $tokoId . ' (type: ' . gettype($tokoId) . ')',
            'produk_id' => $produkId . ' (type: ' . gettype($produkId) . ')',
            'merk_id' => $merkId,
            'quantity' => $quantity,
        ]);

        // Find existing stock record by MERK + STORE (grouped, not per individual product)
        // Join with produk table to find any product with same merk in the same store
        $existingStokByMerk = ProdukStok::where('owner_id', $ownerId)
            ->where('pos_toko_id', $tokoId)
            ->whereHas('produk', function($query) use ($merkId) {
                $query->where('pos_produk_merk_id', $merkId);
            })
            ->first();

        \Log::info('updateProductStock - Checking existing record by MERK:', [
            'exists' => $existingStokByMerk ? 'YES - ID: ' . $existingStokByMerk->id : 'NO',
            'merk_id' => $merkId,
            'current_stock' => $existingStokByMerk ? $existingStokByMerk->stok : null,
        ]);

        // If existing stock for this merk+store found, use it
        // Otherwise create new stock entry with this product as representative
        if ($existingStokByMerk) {
            $stok = $existingStokByMerk;
        } else {
            $stok = ProdukStok::create([
                'owner_id' => $ownerId,
                'pos_toko_id' => $tokoId,
                'pos_produk_id' => $produkId, // First product becomes representative
                'stok' => 0,
            ]);
        }

        \Log::info('updateProductStock - Stock record (grouped by merk):', [
            'id' => $stok->id,
            'was_existing' => $existingStokByMerk ? true : false,
            'representative_produk_id' => $stok->pos_produk_id,
            'current_stock' => $stok->stok,
        ]);

        $stokSebelum = $stok->stok;
        $stokSesudah = $stokSebelum + $quantity;

        // Prevent negative stock
        if ($stokSesudah < 0) {
            return false;
        }

        // Update stock
        $stok->update(['stok' => $stokSesudah]);

        // Create log
        $user = auth()->user();
        LogStok::create([
            'owner_id' => $ownerId,
            'pos_produk_id' => $produkId,
            'pos_toko_id' => $tokoId,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'perubahan' => $quantity,
            'tipe' => $tipe,
            'referensi' => $referensi,
            'keterangan' => $keterangan,
            'pos_pengguna_id' => $user ? $user->id : null,
        ]);

        return true;
    }

    /**
     * Process transaction items and update stock
     * 
     * @param array $items
     * @param int $ownerId
     * @param int $tokoId
     * @param bool $isTransaksiMasuk
     * @param string $invoice
     * @return void
     */
    protected function processTransactionItems($items, $ownerId, $tokoId, $isTransaksiMasuk, $invoice)
    {
        foreach ($items as $item) {
            if (isset($item['pos_produk_id']) && $item['pos_produk_id']) {
                $quantity = $item['quantity'] ?? 0;
                
                // For incoming transaction (sales), reduce stock
                // For outgoing transaction (purchases), increase stock
                $stockChange = $isTransaksiMasuk ? -$quantity : $quantity;
                $tipe = $isTransaksiMasuk ? 'keluar' : 'masuk';
                
                $this->updateProductStock(
                    $ownerId,
                    $tokoId,
                    $item['pos_produk_id'],
                    $stockChange,
                    $tipe,
                    $invoice,
                    $isTransaksiMasuk ? 'Penjualan produk' : 'Pembelian produk dari supplier'
                );
            }
        }
    }
}
