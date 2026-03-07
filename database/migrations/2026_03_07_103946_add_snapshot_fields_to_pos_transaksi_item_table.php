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
        Schema::table('pos_transaksi_item', function (Blueprint $table) {
            // Add snapshot fields to preserve product data even after product deletion
            $table->string('imei', 255)->nullable()->after('pos_service_id');
            $table->string('product_name', 255)->nullable()->after('imei');
            $table->string('product_type', 50)->nullable()->after('product_name');
            $table->string('merk_name', 100)->nullable()->after('product_type');
            $table->string('warna', 100)->nullable()->after('merk_name');
            $table->string('penyimpanan', 50)->nullable()->after('warna');
            $table->string('ram', 50)->nullable()->after('penyimpanan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_transaksi_item', function (Blueprint $table) {
            $table->dropColumn([
                'imei',
                'product_name',
                'product_type',
                'merk_name',
                'warna',
                'penyimpanan',
                'ram',
            ]);
        });
    }
};
