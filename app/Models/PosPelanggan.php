<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosPelanggan extends Model
{
    use HasFactory;

    protected $table = 'pos_pelanggan';

    protected $fillable = [
        'owner_id',
        'nama',
        'telepon',
        'alamat',
        'email',
    ];

    // Relationship to owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relationship to transactions
    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'pos_pelanggan_id');
    }
}
