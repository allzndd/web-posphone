<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosRam extends Model
{
    use HasFactory;

    protected $table = 'pos_ram';

    protected $fillable = [
        'id_owner',
        'kapasitas',
        'is_global',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Ram belongs to Owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'id_owner');
    }
}
