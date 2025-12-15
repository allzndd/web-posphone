<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSupplier extends Model
{
    use HasFactory;

    protected $table = 'pos_supplier';

    protected $fillable = [
        'owner_id',
        'nama',
        'slug',
        'nomor_hp',
        'telepon',
        'alamat',
        'email',
        'keterangan',
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

    /**
     * Get the owner that owns the supplier.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the transactions for the supplier.
     */
    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'pos_supplier_id');
    }
}
