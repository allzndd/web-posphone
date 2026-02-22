<?php

namespace App\Traits;

use App\Models\ProdukStok;
use App\Models\LogStok;

trait UpdatesStock
{
    /**
     * Update product stock and create log
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
        // Log input parameters
        \Log::info('updateProductStock - Input parameters:', [
            'owner_id' => $ownerId . ' (type: ' . gettype($ownerId) . ')',
            'toko_id' => $tokoId . ' (type: ' . gettype($tokoId) . ')',
            'produk_id' => $produkId . ' (type: ' . gettype($produkId) . ')',
            'quantity' => $quantity,
        ]);

        // Check if record exists BEFORE firstOrCreate
        $existingRecord = ProdukStok::where('owner_id', $ownerId)
            ->where('pos_toko_id', $tokoId)
            ->where('pos_produk_id', $produkId)
            ->first();

        \Log::info('updateProductStock - Checking existing record:', [
            'exists' => $existingRecord ? 'YES - ID: ' . $existingRecord->id : 'NO',
            'current_stock' => $existingRecord ? $existingRecord->stok : null,
        ]);

        // Find or create stock record
        $stok = ProdukStok::firstOrCreate(
            [
                'owner_id' => $ownerId,
                'pos_toko_id' => $tokoId,
                'pos_produk_id' => $produkId,
            ],
            [
                'stok' => 0,
            ]
        );

        \Log::info('updateProductStock - After firstOrCreate:', [
            'id' => $stok->id,
            'was_just_created' => $stok->wasRecentlyCreated,
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
