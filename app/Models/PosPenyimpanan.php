<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosPenyimpanan extends Model
{
    use HasFactory;

    protected $table = 'pos_penyimpanan';

    protected $fillable = [
        'id_owner',
        'pos_produk_id',
        'kapasitas',
        'id_global',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Penyimpanan belongs to Owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'id_owner');
    }
}
