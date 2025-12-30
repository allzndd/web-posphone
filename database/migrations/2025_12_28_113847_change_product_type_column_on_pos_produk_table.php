<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_produk', function (Blueprint $table) {
            // ubah enum -> varchar(100)
            $table->string('product_type', 100)
                  ->default('electronic')
                  ->change();
        });
    }

    public function down()
    {
        Schema::table('pos_produk', function (Blueprint $table) {
            // rollback ke enum semula
            $table->enum('product_type', ['electronic', 'accessory', 'service'])
                  ->default('electronic')
                  ->change();
        });
    }
};
