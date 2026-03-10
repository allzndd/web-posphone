<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_log_stok', function (Blueprint $table) {
            $table->string('nama_produk', 255)->nullable()->after('pos_toko_id');
            $table->string('imei', 100)->nullable()->after('nama_produk');
        });
    }

    public function down()
    {
        Schema::table('pos_log_stok', function (Blueprint $table) {
            $table->dropColumn(['nama_produk', 'imei']);
        });
    }
};
