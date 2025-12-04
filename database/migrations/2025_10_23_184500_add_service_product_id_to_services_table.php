<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('service_product_id')->nullable()->after('imei')->constrained('products')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['service_product_id']);
            $table->dropColumn('service_product_id');
        });
    }
};
