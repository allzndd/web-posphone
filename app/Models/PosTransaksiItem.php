<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTransaksiItem extends Model
{
    use HasFactory;

    protected $table = 'pos_transaksi_item';

    public $timestamps = false;

    protected $fillable = [
        'pos_transaksi_id',
        'pos_produk_id',
        'pos_service_id',
        'quantity',
        'harga_satuan',
        'subtotal',
        'diskon',
        'garansi',
        'garansi_expires_at',
        'pajak',
    ];

    protected $casts = [
        'pos_transaksi_id' => 'integer',
        'pos_produk_id' => 'integer',
        'pos_service_id' => 'integer',
        'quantity' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'garansi' => 'integer',
        'garansi_expires_at' => 'date',
        'pajak' => 'decimal:2',
    ];

    /**
     * Get the transaksi that owns the item.
     */
    public function transaksi()
    {
        return $this->belongsTo(PosTransaksi::class, 'pos_transaksi_id');
    }

    /**
     * Get the product.
     */
    public function produk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_id');
    }

    /**
     * Get the service.
     */
    public function service()
    {
        return $this->belongsTo(PosService::class, 'pos_service_id');
    }
}
