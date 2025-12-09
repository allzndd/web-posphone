<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'pos_service';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'pos_pelanggan_id',
        'nama',
        'keterangan',
        'harga',
        'durasi',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
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

    public function pelanggan()
    {
        return $this->belongsTo(PosPelanggan::class, 'pos_pelanggan_id');
    }

    /**
     * Scope untuk layanan inactive
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Check if service is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Check if service is inactive
     */
    public function isInactive()
    {
        return $this->status === 'Inactive';
    }
}
