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
        return $this->belongsTo(User::class, 'owner_id');
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
}
