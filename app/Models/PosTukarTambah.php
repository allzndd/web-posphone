<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTukarTambah extends Model
{
    use HasFactory;

    protected $table = 'pos_tukar_tambah';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'pos_pelanggan_id',
        'pos_produk_masuk_id',
        'pos_produk_keluar_id',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(PosPelanggan::class, 'pos_pelanggan_id');
    }

    public function produkMasuk()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_masuk_id');
    }

    public function produkKeluar()
    {
        return $this->belongsTo(PosProduk::class, 'pos_produk_keluar_id');
    }

    public function transaksi()
    {
        return $this->hasOne(PosTransaksi::class, 'pos_tukar_tambah_id', 'id');
    }
}
