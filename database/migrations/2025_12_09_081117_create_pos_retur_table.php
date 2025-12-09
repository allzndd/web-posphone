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
        Schema::create('pos_retur', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->integer('pos_transaksi_id')->nullable();
            $table->integer('pos_transaksi_item_id')->nullable();
            $table->integer('pos_produk_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('nominal_retur', 15, 2)->nullable();
            $table->string('alasan')->nullable();
            $table->string('status', 45)->nullable();
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
        Schema::dropIfExists('pos_retur');
    }
};
