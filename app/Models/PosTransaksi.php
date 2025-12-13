<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTransaksi extends Model
{
    use HasFactory;

    protected $table = 'pos_transaksi';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'pos_pelanggan_id',
        'pos_tukar_tambah_id',
        'pos_supplier_id',
        'is_transaksi_masuk',
        'invoice',
        'total_harga',
        'keterangan',
        'status',
        'metode_pembayaran',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'is_transaksi_masuk' => 'integer',
    ];

    // Relationship to owner (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relationship to toko
    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    // Relationship to pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(PosPelanggan::class, 'pos_pelanggan_id');
    }

    // Relationship to tukar tambah
    public function tukarTambah()
    {
        return $this->belongsTo(PosTukarTambah::class, 'pos_tukar_tambah_id');
    }

    // Relationship to supplier
    public function supplier()
    {
        return $this->belongsTo(PosSupplier::class, 'pos_supplier_id');
    }

    // Relationship to transaksi details (assuming pos_transaksi_detail table exists)
    public function details()
    {
        return $this->hasMany(PosTransaksiDetail::class, 'pos_transaksi_id');
    }
}
