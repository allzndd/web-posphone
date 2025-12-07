<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KelolaOwner extends Model
{
    use HasFactory;

    protected $table = 'kelola_owner';

    protected $fillable = [
        'nama_perusahaan',
        'nama_pemilik',
        'email',
        'telepon',
        'paket',
        'jumlah_outlet',
        'tanggal_daftar',
        'tanggal_expired',
        'status',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_expired' => 'date',
        'jumlah_outlet' => 'integer',
    ];

    /**
     * Scope untuk owner aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope untuk owner expired
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'Expired');
    }

    /**
     * Check if owner is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Check if owner is expired
     */
    public function isExpired()
    {
        return $this->status === 'Expired';
    }

    /**
     * Get days until expiration
     */
    public function daysUntilExpiration()
    {
        return Carbon::now()->diffInDays($this->tanggal_expired, false);
    }

    /**
     * Check if expiring soon (within 30 days)
     */
    public function isExpiringSoon()
    {
        $days = $this->daysUntilExpiration();
        return $days >= 0 && $days <= 30;
    }
}
