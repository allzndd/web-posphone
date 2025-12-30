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
        'pos_toko_id',
        'pos_produk_nama_id',
        'pos_produk_merk_id',
        'pos_produk_tipe_id',
        'product_type',
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
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
    /**
     * Generate slug from merk nama + last 3 digits of IMEI
     */
    protected static function generateSlug($model)
    {
        $merkNama = '';
        if ($model->pos_produk_merk_id) {
            $merk = PosProdukMerk::find($model->pos_produk_merk_id);
            $merkNama = $merk ? Str::slug($merk->nama) : 'produk';
        } else {
            $merkNama = 'produk';
        }

        $imeiSuffix = $model->imei ? substr($model->imei, -3) : rand(100, 999);
        $baseSlug = $merkNama . '-' . $imeiSuffix;
        
        // Make slug unique by adding counter if needed
        $slug = $baseSlug;
        $counter = 1;
        while (self::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = self::generateSlug($model);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('pos_produk_merk_id') || $model->isDirty('imei')) {
                $model->slug = self::generateSlug($model);
            }
        });

        // Cascade delete: when product is deleted, delete related stock and logs
        static::deleting(function ($model) {
            // Delete all stock records for this product
            $model->stok()->delete();
            
            // Delete all log stok records for this product
            \App\Models\LogStok::where('pos_produk_id', $model->id)->delete();
        });
    }

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    public function nama()
    {
        return $this->belongsTo(PosProdukNama::class, 'pos_produk_nama_id');
    }

    public function merk()
    {
        return $this->belongsTo(PosProdukMerk::class, 'pos_produk_merk_id');
    }

    public function tipe()
    {
        return $this->belongsTo(PosProdukTipe::class, 'pos_produk_tipe_id');
    }

    public function stok()
    {
        return $this->hasMany(ProdukStok::class, 'pos_produk_id');
    }
}
