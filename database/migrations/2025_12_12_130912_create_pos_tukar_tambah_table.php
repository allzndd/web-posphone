<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_tukar_tambah', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->integer('pos_toko_id')->nullable();
            $table->integer('pos_pelanggan_id')->nullable();
            $table->integer('pos_produk_masuk_id')->nullable();
            $table->integer('pos_produk_keluar_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_tukar_tambah');
    }
};
