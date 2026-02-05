<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosWarna extends Model
{
    use HasFactory;

    protected $table = 'pos_warna';

    protected $fillable = [
        'id_owner',
        'pos_produk_id',
        'warna',
        'is_global',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Warna belongs to Owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'id_owner');
    }
}
