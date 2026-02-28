<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosProdukBiayaTambahan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pos_produk_biaya_tambahan';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pos_produk_id',
        'nama',
        'harga',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Get the product that owns this additional cost.
     */
    public function produk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_id');
    }

    /**
     * Scope a query to only include costs for a specific product.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $produkId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProduk($query, $produkId)
    {
        return $query->where('pos_produk_id', $produkId);
    }

    /**
     * Get the total additional cost for this product.
     *
     * @return float
     */
    public function getTotalBiayaTambahanAttribute()
    {
        return $this->harga ?? 0;
    }

    /**
     * Get formatted price.
     *
     * @return string
     */
    public function getFormattedHargaAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }
}