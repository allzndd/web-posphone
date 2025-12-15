<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosPelanggan extends Model
{
    use HasFactory;

    protected $table = 'pos_pelanggan';

    protected $fillable = [
        'owner_id',
        'nama',
        'slug',
        'nomor_hp',
        'email',
        'alamat',
        'tanggal_bergabung',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
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
                $model->slug = \Illuminate\Support\Str::slug($model->nama);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama')) {
                $model->slug = \Illuminate\Support\Str::slug($model->nama);
            }
        });
    }

    // Relationship to owner
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    // Relationship to transactions
    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'pos_pelanggan_id');
    }
}
