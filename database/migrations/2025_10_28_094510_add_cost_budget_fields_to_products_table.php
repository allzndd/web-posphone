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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->default(0)->after('buy_price')->comment('Modal/Base Cost');
            $table->decimal('additional_cost', 15, 2)->default(0)->after('cost')->comment('Biaya Tambahan');
            $table->string('barre_health')->nullable()->after('additional_cost')->comment('Battery Health, e.g. 85%');
            $table->string('storage')->nullable()->after('color')->comment('Storage Capacity e.g. 128GB, 256GB');
            $table->integer('view_count')->default(0)->after('stock')->comment('Product View Count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost', 'additional_cost', 'barre_health', 'storage', 'view_count']);
        });
    }
};
