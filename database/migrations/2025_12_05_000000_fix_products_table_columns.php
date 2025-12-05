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
            // Check and add sell_price if missing
            if (!Schema::hasColumn('products', 'sell_price')) {
                $table->decimal('sell_price', 15, 2)->after('buy_price');
            }
            
            // Check and add buy_price if missing
            if (!Schema::hasColumn('products', 'buy_price')) {
                $table->decimal('buy_price', 15, 2)->after('assessoris');
            }
            
            // Check and add gross_profit if missing
            if (!Schema::hasColumn('products', 'gross_profit')) {
                $table->decimal('gross_profit', 15, 2)->after('sell_price');
            }
            
            // Check and add net_profit if missing
            if (!Schema::hasColumn('products', 'net_profit')) {
                $table->decimal('net_profit', 15, 2)->after('gross_profit');
            }
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
            if (Schema::hasColumn('products', 'sell_price')) {
                $table->dropColumn('sell_price');
            }
            if (Schema::hasColumn('products', 'buy_price')) {
                $table->dropColumn('buy_price');
            }
            if (Schema::hasColumn('products', 'gross_profit')) {
                $table->dropColumn('gross_profit');
            }
            if (Schema::hasColumn('products', 'net_profit')) {
                $table->dropColumn('net_profit');
            }
        });
    }
};
