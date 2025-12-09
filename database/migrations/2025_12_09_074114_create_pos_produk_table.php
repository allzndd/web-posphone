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
        Schema::create('pos_produk', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->integer('pos_produk_merk_id')->nullable();
            $table->string('nama')->nullable();
            $table->string('slug')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('warna')->nullable();
            $table->string('penyimpanan')->nullable();
            $table->string('battery_health')->nullable();
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->json('biaya_tambahan')->nullable();
            $table->string('imei')->nullable();
            $table->string('aksesoris', 45)->nullable();
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
        Schema::dropIfExists('pos_produk');
    }
};
