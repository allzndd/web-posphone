<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosProdukMerk extends Model
{
    use HasFactory;

    protected $table = 'pos_produk_merk';

    protected $fillable = [
        'owner_id',
        'merk',
        'nama',
        'slug',
        'product_type',
        'is_global',
        'service_name',
        'service_duration',
        'service_period',
        'service_description',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

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

    public function produk()
    {
        return $this->hasMany(PosProduk::class, 'pos_produk_merk_id');
    }
}
