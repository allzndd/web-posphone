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
        Schema::create('pos_produk_biaya_tambahan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_produk_id');
            $table->string('nama'); // Description of the add-on cost
            $table->decimal('harga', 15, 2)->default(0); // Amount
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('pos_produk_id')
                  ->references('id')
                  ->on('pos_produk')
                  ->onDelete('cascade');
            
            // Index for faster queries
            $table->index('pos_produk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_produk_biaya_tambahan');
    }
};
