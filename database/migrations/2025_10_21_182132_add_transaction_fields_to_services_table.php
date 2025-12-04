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
            $table->foreignId('customer_id')->nullable()->after('id')->constrained('customers')->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->after('customer_id')->constrained('transactions')->cascadeOnDelete();
            $table->date('service_date')->nullable()->after('transaction_id');
            $table->string('imei')->nullable()->after('service_date')->comment('IMEI of device being serviced');
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
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['transaction_id']);
            $table->dropColumn(['customer_id', 'transaction_id', 'service_date', 'imei']);
        });
    }
};
