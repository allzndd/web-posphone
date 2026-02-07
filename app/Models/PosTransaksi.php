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
        'pos_kategori_expense_id',
        'is_transaksi_masuk',
        'invoice',
        'total_harga',
        'keterangan',
        'status',
        'metode_pembayaran',
        'payment_status',
        'paid_amount',
        'due_date',
        'payment_terms',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'is_transaksi_masuk' => 'integer',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    // Relationship to owner
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
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

    // Relationship to kategori expense
    public function kategoriExpense()
    {
        return $this->belongsTo(PosKategoriExpense::class, 'pos_kategori_expense_id');
    }

    // Relationship to transaksi items
    public function items()
    {
        return $this->hasMany(PosTransaksiItem::class, 'pos_transaksi_id');
    }
}
