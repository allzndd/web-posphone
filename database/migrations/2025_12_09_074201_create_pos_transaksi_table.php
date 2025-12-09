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
        Schema::create('pos_transaksi', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->string('pos_toko_id', 45)->nullable();
            $table->integer('pos_pelanggan_id')->nullable();
            $table->integer('pos_supplier_id')->nullable();
            $table->integer('is_transaksi_masuk')->nullable();
            $table->string('invoice', 45)->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->string('keterangan')->nullable();
            $table->string('status', 45)->nullable();
            $table->string('metode_pembayaran', 45)->nullable();
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
        Schema::dropIfExists('pos_transaksi');
    }
};
