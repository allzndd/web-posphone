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

    /**
     * Check if subscription is trial and expired
     */
    public function isTrialExpired()
    {
        return $this->is_trial == 1 && $this->isExpired();
    }

    /**
     * Downgrade subscription to free tier
     */
    public function downgradeToFreeTier()
    {
        try {
            // Get free tier package
            $freePackage = TipeLayanan::where('slug', 'free')->first();
            
            if (!$freePackage) {
                // If free tier doesn't exist, create it
                $freePackage = TipeLayanan::create([
                    'nama' => 'Free Tier',
                    'slug' => 'free',
                    'harga' => 0,
                    'durasi' => 0,
                    'durasi_satuan' => 'bulan',
                ]);
            }

            // Update subscription to free tier
            $this->update([
                'tipe_layanan_id' => $freePackage->id,
                'is_trial' => 0,
                'is_active' => 1, // Keep active to allow access to free tier features
                'end_date' => null, // No expiration for free tier
            ]);

            \Log::info('Subscription downgraded to free tier', [
                'langganan_id' => $this->id,
                'owner_id' => $this->owner_id,
                'tipe_layanan_id' => $freePackage->id,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to downgrade to free tier: ' . $e->getMessage(), [
                'langganan_id' => $this->id,
                'owner_id' => $this->owner_id,
            ]);

            return false;
        }
    }
}
