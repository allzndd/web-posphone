<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_produk_stok', function (Blueprint $table) {
            $table->string('merk_name')->nullable()->after('stok');
        });
    }

    public function down()
    {
        Schema::table('pos_produk_stok', function (Blueprint $table) {
            $table->dropColumn('merk_name');
        });
    }
};
