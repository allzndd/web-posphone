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
