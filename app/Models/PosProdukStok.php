<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosProdukStok extends Model
{
    use HasFactory;

    protected $table = 'pos_produk_stok';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'pos_produk_id',
        'stok'
    ];

    protected $casts = [
        'stok' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    public function produk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_id');
    }
}
