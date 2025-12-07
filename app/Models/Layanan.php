<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Scope untuk layanan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
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
