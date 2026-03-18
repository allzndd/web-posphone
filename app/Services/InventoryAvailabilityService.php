<?php

namespace App\Services;

use App\Models\PosProduk;
use App\Models\PosTransaksiItem;
use App\Models\ProdukStok;
use Illuminate\Support\Collection;

class InventoryAvailabilityService
{
    /**
     * Get product IDs that were sold in completed sales transactions.
     */
    public static function getSoldProductIds(?int $ownerId, ?int $storeId = null): array
    {
        if (empty($ownerId)) {
            return [];
        }

        $query = PosTransaksiItem::query()
            ->join('pos_transaksi', 'pos_transaksi.id', '=', 'pos_transaksi_item.pos_transaksi_id')
            ->where('pos_transaksi.owner_id', $ownerId)
            ->where('pos_transaksi.is_transaksi_masuk', 1)
            ->where('pos_transaksi.status', 'completed')
            ->whereNotNull('pos_transaksi_item.pos_produk_id');

        if (!is_null($storeId)) {
            $query->where('pos_transaksi.pos_toko_id', $storeId);
        }

        return $query
            ->distinct()
            ->pluck('pos_transaksi_item.pos_produk_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();
    }

    /**
     * Get currently available product IDs from grouped stock records.
     *
     * Availability logic:
     * - Uses grouped stock count per merk+store as source of truth.
     * - Excludes products already sold in completed sales transactions.
     * - If candidate units exceed grouped stock (legacy inconsistent data),
     *   keeps the newest units up to stock count.
     */
    public static function getAvailableProductIds(?int $ownerId, ?int $storeId = null): array
    {
        if (empty($ownerId)) {
            return [];
        }

        $stockEntries = ProdukStok::where('owner_id', $ownerId)
            ->where('stok', '>', 0)
            ->when(!is_null($storeId), function ($query) use ($storeId) {
                return $query->where('pos_toko_id', $storeId);
            })
            ->with('produk')
            ->get();

        if ($stockEntries->isEmpty()) {
            return [];
        }

        $soldProductIds = self::getSoldProductIds($ownerId);
        $availableIds = [];

        foreach ($stockEntries as $stockEntry) {
            $merkId = $stockEntry->produk ? (int) $stockEntry->produk->pos_produk_merk_id : 0;
            $stockCount = max(0, (int) $stockEntry->stok);

            if ($merkId < 1 || $stockCount < 1) {
                continue;
            }

            $productQuery = PosProduk::where('owner_id', $ownerId)
                ->where('pos_produk_merk_id', $merkId)
                ->where(function ($query) use ($stockEntry) {
                    $query->where('pos_toko_id', $stockEntry->pos_toko_id)
                        ->orWhereNull('pos_toko_id');
                });

            if (!empty($soldProductIds)) {
                $productQuery->whereNotIn('id', $soldProductIds);
            }

            $ids = $productQuery
                ->orderBy('id', 'desc')
                ->limit($stockCount)
                ->pluck('id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->all();

            if (!empty($ids)) {
                $availableIds = array_merge($availableIds, $ids);
            }
        }

        return array_values(array_unique($availableIds));
    }

    /**
     * Get available product rows for a single grouped stock entry.
     */
    public static function getAvailableProductsForStockEntry(ProdukStok $stockEntry, array $withRelations = []): Collection
    {
        $ownerId = (int) ($stockEntry->owner_id ?? 0);
        $stockCount = max(0, (int) ($stockEntry->stok ?? 0));
        $merkId = $stockEntry->produk ? (int) $stockEntry->produk->pos_produk_merk_id : 0;

        if ($ownerId < 1 || $stockCount < 1 || $merkId < 1) {
            return collect();
        }

        $soldProductIds = self::getSoldProductIds($ownerId);

        $query = PosProduk::where('owner_id', $ownerId)
            ->where('pos_produk_merk_id', $merkId)
            ->where(function ($builder) use ($stockEntry) {
                $builder->where('pos_toko_id', $stockEntry->pos_toko_id)
                    ->orWhereNull('pos_toko_id');
            });

        if (!empty($withRelations)) {
            $query->with($withRelations);
        }

        if (!empty($soldProductIds)) {
            $query->whereNotIn('id', $soldProductIds);
        }

        return $query
            ->orderBy('id', 'desc')
            ->limit($stockCount)
            ->get();
    }

    /**
     * Check if one specific product can still be used as product OUT.
     */
    public static function isProductAvailableForSale(?int $ownerId, ?int $storeId, ?int $productId): bool
    {
        if (empty($ownerId) || empty($storeId) || empty($productId)) {
            return false;
        }

        $availableIds = self::getAvailableProductIds((int) $ownerId, (int) $storeId);

        return in_array((int) $productId, $availableIds, true);
    }
}
