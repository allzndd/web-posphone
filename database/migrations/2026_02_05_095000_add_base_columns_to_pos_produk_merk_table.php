<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_produk_merk', function (Blueprint $table) {
            $table->string('merk', 255)->nullable()->after('owner_id')->comment('Brand name');
            $table->string('product_type', 50)->default('electronic')->after('merk')->comment('Type: electronic, accessories, service');
            $table->boolean('is_global')->default(0)->after('slug')->comment('Is global product');
        });
    }

    public function down()
    {
        Schema::table('pos_produk_merk', function (Blueprint $table) {
            $table->dropColumn(['merk', 'product_type', 'is_global']);
        });
    }
};
