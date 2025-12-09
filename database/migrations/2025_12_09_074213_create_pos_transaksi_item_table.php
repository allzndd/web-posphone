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
        Schema::create('pos_transaksi_item', function (Blueprint $table) {
            $table->id();
            $table->integer('pos_transaksi_id')->nullable();
            $table->integer('pos_produk_id')->nullable();
            $table->integer('pos_service_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('diskon', 15, 2)->nullable();
            $table->integer('garansi')->nullable();
            $table->date('garansi_expires_at')->nullable();
            $table->decimal('pajak', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_transaksi_item');
    }
};
