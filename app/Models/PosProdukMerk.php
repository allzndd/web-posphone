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
        'nama',
        'slug',
    ];

    protected $casts = [
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

    public function produk()
    {
        return $this->hasMany(PosProduk::class, 'pos_produk_merk_id');
    }
}
