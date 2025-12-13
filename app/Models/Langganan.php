<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langganan extends Model
{
    use HasFactory;

    protected $table = 'langganan';

    protected $fillable = [
        'owner_id',
        'tipe_layanan_id',
        'is_active',
        'is_trial',
        'started_date',
        'end_date',
    ];

    protected $casts = [
        'started_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'integer',
        'is_trial' => 'integer',
    ];

    /**
     * Get the owner that owns the subscription.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the service package type.
     */
    public function tipeLayanan()
    {
        return $this->belongsTo(TipeLayanan::class, 'tipe_layanan_id');
    }

    /**
     * Get all payments for this subscription.
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'langganan_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->is_active == 1;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return $this->end_date < now();
    }
}
