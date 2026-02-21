<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pos_produk_merk', function (Blueprint $table) {
            // Add service-specific fields
            $table->string('service_name', 255)->nullable()->after('product_type')->comment('Service name for service type products');
            $table->unsignedInteger('service_duration')->nullable()->after('service_name')->comment('Service duration value');
            $table->string('service_period', 50)->nullable()->after('service_duration')->comment('Service period: days, weeks, months, years');
            $table->longText('service_description')->nullable()->after('service_period')->comment('Detailed service description');
        });
    }

    public function down()
    {
        Schema::table('pos_produk_merk', function (Blueprint $table) {
            $table->dropColumn(['service_name', 'service_duration', 'service_period', 'service_description']);
        });
    }
};
