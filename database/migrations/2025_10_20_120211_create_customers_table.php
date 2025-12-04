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
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('tradein_id')->nullable()->constrained('trade_ins')->nullOnDelete();
                $table->string('name');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->text('address');
                $table->date('join_date');
                $table->timestamps();
            });
        } else {
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('customers', 'status')) {
                Schema::table('customers', function (Blueprint $table) {
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('address');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
