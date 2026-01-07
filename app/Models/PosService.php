<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosService extends Model
{
    use HasFactory;

    protected $table = 'pos_service';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'nama',
        'keterangan',
        'harga',
        'durasi',
    ];

    protected $casts = [
        'owner_id' => 'integer',
        'pos_toko_id' => 'integer',
        'harga' => 'decimal:2',
        'durasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the service.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the store.
     */
    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    /**
     * Get formatted price.
     */
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurasiAttribute()
    {
        if ($this->durasi < 60) {
            return $this->durasi . ' minutes';
        }
        
        $hours = floor($this->durasi / 60);
        $minutes = $this->durasi % 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $hours . ' hours';
    }
}
