<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add masuk transaction permissions
        DB::table('permissions')->insertOrIgnore([
            ['nama' => 'transaksi.masuk.read', 'deskripsi' => 'Akses untuk melihat daftar transaksi masuk (penjualan)', 'modul' => 'transaksi', 'aksi' => 'read', 'created_at' => now()],
            ['nama' => 'transaksi.masuk.create', 'deskripsi' => 'Akses untuk membuat transaksi masuk (penjualan) baru', 'modul' => 'transaksi', 'aksi' => 'create', 'created_at' => now()],
            ['nama' => 'transaksi.masuk.update', 'deskripsi' => 'Akses untuk mengedit transaksi masuk (penjualan)', 'modul' => 'transaksi', 'aksi' => 'update', 'created_at' => now()],
            ['nama' => 'transaksi.masuk.delete', 'deskripsi' => 'Akses untuk menghapus transaksi masuk (penjualan)', 'modul' => 'transaksi', 'aksi' => 'delete', 'created_at' => now()],
        ]);

        // Add keluar transaction permissions
        DB::table('permissions')->insertOrIgnore([
            ['nama' => 'transaksi.keluar.read', 'deskripsi' => 'Akses untuk melihat daftar transaksi keluar (pembelian)', 'modul' => 'transaksi', 'aksi' => 'read', 'created_at' => now()],
            ['nama' => 'transaksi.keluar.create', 'deskripsi' => 'Akses untuk membuat transaksi keluar (pembelian) baru', 'modul' => 'transaksi', 'aksi' => 'create', 'created_at' => now()],
            ['nama' => 'transaksi.keluar.update', 'deskripsi' => 'Akses untuk mengedit transaksi keluar (pembelian)', 'modul' => 'transaksi', 'aksi' => 'update', 'created_at' => now()],
            ['nama' => 'transaksi.keluar.delete', 'deskripsi' => 'Akses untuk menghapus transaksi keluar (pembelian)', 'modul' => 'transaksi', 'aksi' => 'delete', 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove masuk and keluar permissions
        DB::table('permissions')
            ->whereIn('nama', [
                'transaksi.masuk.read',
                'transaksi.masuk.create',
                'transaksi.masuk.update',
                'transaksi.masuk.delete',
                'transaksi.keluar.read',
                'transaksi.keluar.create',
                'transaksi.keluar.update',
                'transaksi.keluar.delete',
            ])
            ->delete();
    }
};
