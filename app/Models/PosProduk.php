<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosProduk extends Model
{
    use HasFactory;

    protected $table = 'pos_produk';

    protected $fillable = [
        'owner_id',
        'pos_produk_merk_id',
        'nama',
        'slug',
        'deskripsi',
        'warna',
        'penyimpanan',
        'battery_health',
        'harga_beli',
        'harga_jual',
        'biaya_tambahan',
        'imei',
        'aksesoris',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'biaya_tambahan' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Auto-generate slug from nama
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nama);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama')) {
                $model->slug = Str::slug($model->nama);
            }
        });
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function merk()
    {
        return $this->belongsTo(PosProdukMerk::class, 'pos_produk_merk_id');
    }
}
