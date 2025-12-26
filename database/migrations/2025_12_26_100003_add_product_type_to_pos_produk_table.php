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
        Schema::table('pos_produk', function (Blueprint $table) {
            $table->enum('product_type', ['electronic', 'accessory', 'service'])->default('electronic')->after('pos_produk_merk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_produk', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
};
