<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owner';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pengguna_id',
    ];

    /**
     * Get the user that owns the owner.
     */
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    /**
     * Get the stores for the owner.
     */
    public function toko()
    {
        return $this->hasMany(PosToko::class, 'owner_id');
    }

    /**
     * Get the products for the owner.
     */
    public function produk()
    {
        return $this->hasMany(PosProduk::class, 'owner_id');
    }

    /**
     * Get the transactions for the owner.
     */
    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'owner_id');
    }
}
