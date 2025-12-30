<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_service', function (Blueprint $table) {
            $table->dropColumn('pos_pelanggan_id');
        });
    }

    public function down()
    {
        Schema::table('pos_service', function (Blueprint $table) {
            $table->integer('pos_pelanggan_id')->nullable()
                  ->after('pos_toko_id');
        });
    }
};
