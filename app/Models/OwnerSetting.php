<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerSetting extends Model
{
    use HasFactory;

    protected $table = 'owner_settings';

    protected $fillable = [
        'owner_id',
        'currency',
    ];

    /**
     * Get the owner that owns the settings.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbolAttribute()
    {
        return match($this->currency) {
            'IDR' => 'Rp',
            'MYR' => 'RM',
            'USD' => '$',
            default => 'Rp',
        };
    }

    /**
     * Get currency name
     */
    public function getCurrencyNameAttribute()
    {
        return match($this->currency) {
            'IDR' => 'Indonesian Rupiah',
            'MYR' => 'Malaysian Ringgit',
            'USD' => 'US Dollar',
            default => 'Indonesian Rupiah',
        };
    }
}
