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
        // Add report permissions
        DB::table('permissions')->insertOrIgnore([
            ['nama' => 'report.read', 'deskripsi' => 'Akses untuk melihat laporan', 'created_at' => now()],
            ['nama' => 'report.export', 'deskripsi' => 'Akses untuk export laporan ke Excel', 'created_at' => now()],
            ['nama' => 'log-stok.read', 'deskripsi' => 'Akses untuk melihat stock history/log stok', 'created_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove report and log-stok permissions
        DB::table('permissions')
            ->whereIn('nama', ['report.read', 'report.export', 'log-stok.read'])
            ->delete();
    }
};

