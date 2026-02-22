<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosToko extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pos_toko';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'nama',
        'slug',
        'alamat',
        'modal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'owner_id' => 'integer',
        'modal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the store.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the employees (users) for the store.
     */
    public function pengguna()
    {
        return $this->hasMany(PosPengguna::class, 'pos_toko_id');
    }

    /**
     * Get the stock records for the store.
     */
    public function produkStok()
    {
        return $this->hasMany(ProdukStok::class, 'pos_toko_id');
    }

    /**
     * Get the stock logs for the store.
     */
    public function logStok()
    {
        return $this->hasMany(LogStok::class, 'pos_toko_id');
    }

    /**
     * Get the services for the store.
     */
    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'pos_toko_id');
    }
}
