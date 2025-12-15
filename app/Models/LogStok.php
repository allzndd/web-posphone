<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStok extends Model
{
    use HasFactory;

    protected $table = 'pos_log_stok';

    protected $fillable = [
        'owner_id',
        'pos_produk_id',
        'pos_toko_id',
        'stok_sebelum',
        'stok_sesudah',
        'perubahan',
        'tipe',
        'referensi',
        'keterangan',
        'pos_pengguna_id',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'pos_produk_id' => 'integer',
        'pos_toko_id' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
        'perubahan' => 'integer',
        'pos_pengguna_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the log.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the product.
     */
    public function produk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_id');
    }

    /**
     * Get the store.
     */
    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    /**
     * Get the user who made the change.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pos_pengguna_id');
    }

    /**
     * Get badge color based on type.
     */
    public function getTipeBadgeColorAttribute()
    {
        return match($this->tipe) {
            'masuk' => 'green',
            'keluar' => 'red',
            'retur' => 'blue',
            'adjustment' => 'yellow',
            default => 'gray',
        };
    }
}
