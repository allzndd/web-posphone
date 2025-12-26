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
        Schema::table('pos_transaksi', function (Blueprint $table) {
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid')->after('metode_pembayaran');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
            $table->date('due_date')->nullable()->after('paid_amount');
            $table->string('payment_terms')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_transaksi', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_amount', 'due_date', 'payment_terms']);
        });
    }
};
