<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSupplier extends Model
{
    use HasFactory;

    protected $table = 'pos_supplier';

    protected $fillable = [
        'owner_id',
        'nama',
        'telepon',
        'alamat',
        'email',
    ];

    /**
     * Get the owner that owns the supplier.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the transactions for the supplier.
     */
    public function transaksi()
    {
        return $this->hasMany(PosTransaksi::class, 'pos_supplier_id');
    }
}
