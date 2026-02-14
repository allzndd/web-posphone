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
        'durasi_satuan',
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
        $satuan = $this->durasi_satuan ?? 'bulan'; // backward compatibility
        
        if ($satuan === 'hari') {
            $singular = 'Day';
            $plural = 'Days';
        } elseif ($satuan === 'tahun') {
            $singular = 'Year';
            $plural = 'Years';
        } else {
            $singular = 'Month';
            $plural = 'Months';
        }
        
        if ($this->durasi == 1) {
            return '1 ' . $singular;
        } else {
            return $this->durasi . ' ' . $plural;
        }
    }

    /**
     * Relasi ke PackagePermission
     */
    public function packagePermissions()
    {
        return $this->hasMany(PackagePermission::class, 'tipe_layanan_id');
    }
}
