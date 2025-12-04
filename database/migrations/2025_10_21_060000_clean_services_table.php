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
        Schema::table('services', function (Blueprint $table) {
            // Remove columns that shouldn't be in services table
            if (Schema::hasColumn('services', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            if (Schema::hasColumn('services', 'cost')) {
                $table->dropColumn('cost');
            }

            if (Schema::hasColumn('services', 'phone_type')) {
                $table->dropColumn('phone_type');
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
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('phone_type')->nullable();
        });
    }
};
