<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukStok extends Model
{
    use HasFactory;

    protected $table = 'pos_produk_stok';

    public $timestamps = false;

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'pos_produk_id',
        'stok',
        'merk_name',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'pos_toko_id' => 'integer',
        'pos_produk_id' => 'integer',
        'stok' => 'integer',
    ];

    /**
     * Get the owner that owns the stock.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the store that owns the stock.
     */
    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    /**
     * Get the product that owns the stock.
     */
    public function produk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_id');
    }

    /**
     * Get grouped product name (brand + type) from master merk.
     * Fallback to snapshot merk_name or representative product display name.
     */
    public function getGroupedNameAttribute()
    {
        $brand = null;
        $type = null;

        if ($this->produk && $this->produk->merk) {
            $brand = trim((string) ($this->produk->merk->merk ?? ''));
            $type = trim((string) ($this->produk->merk->nama ?? ''));
        }

        $parts = array_values(array_filter([$brand, $type], function ($value) {
            return $value !== '';
        }));

        if (!empty($parts)) {
            if (count($parts) === 2 && strtolower($parts[0]) === strtolower($parts[1])) {
                return $parts[0];
            }

            return implode(' ', $parts);
        }

        if (!empty($this->merk_name)) {
            return $this->merk_name;
        }

        return $this->produk ? $this->produk->display_name : 'Unknown Product';
    }
}
