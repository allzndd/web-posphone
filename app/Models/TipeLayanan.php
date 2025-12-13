<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeLayanan extends Model
{
    use HasFactory;

    protected $table = 'tipe_layanan';

    protected $fillable = [
        'nama',
        'slug',
        'harga',
        'durasi',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'durasi' => 'integer',
    ];

    /**
     * Get active service packages
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('nama');
    }

    /**
     * Get the duration in readable format
     */
    public function getDurationTextAttribute()
    {
        if ($this->durasi == 1) {
            return '1 Month';
        } elseif ($this->durasi == 12) {
            return '1 Year';
        } else {
            return $this->durasi . ' Months';
        }
    }
}
