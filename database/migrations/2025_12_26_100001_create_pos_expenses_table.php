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
        Schema::create('pos_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->integer('pos_toko_id')->nullable();
            $table->enum('expense_type', ['salary', 'rent', 'utilities', 'maintenance', 'marketing', 'transportation', 'other'])->default('other');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->date('expense_date');
            $table->string('receipt_number')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('owner_id');
            $table->index('pos_toko_id');
            $table->index('expense_type');
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_expenses');
    }
};
