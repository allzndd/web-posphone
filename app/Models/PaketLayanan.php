<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketLayanan extends Model
{
    use HasFactory;

    protected $table = 'paket_layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'durasi',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Scope untuk paket aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope untuk paket inactive
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'Inactive');
    }

    /**
     * Check if package is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Check if package is inactive
     */
    public function isInactive()
    {
        return $this->status === 'Inactive';
    }
}
