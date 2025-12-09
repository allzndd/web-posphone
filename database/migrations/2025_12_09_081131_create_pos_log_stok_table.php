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
        Schema::create('pos_log_stok', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->integer('pos_produk_id')->nullable();
            $table->integer('pos_toko_id')->nullable();
            $table->integer('stok_sebelum')->nullable();
            $table->integer('stok_sesudah')->nullable();
            $table->integer('perubahan')->nullable();
            $table->string('tipe', 45)->nullable()->comment('masuk/keluar/retur/adjustment');
            $table->string('referensi', 100)->nullable()->comment('ID transaksi atau keterangan');
            $table->string('keterangan')->nullable();
            $table->integer('pos_pengguna_id')->nullable()->comment('User yang melakukan perubahan');
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
        Schema::dropIfExists('pos_log_stok');
    }
};
