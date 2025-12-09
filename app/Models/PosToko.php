<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosToko extends Model
{
    use HasFactory;

    protected $table = 'pos_toko';

    protected $fillable = [
        'owner_id',
        'nama',
        'slug',
        'alamat'
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
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function pengguna()
    {
        return $this->hasMany(PosPengguna::class, 'pos_toko_id');
    }

    public function produk()
    {
        return $this->hasMany(PosProduk::class, 'pos_toko_id');
    }

    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'pos_toko_id');
    }
}
