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
        // 1. Create customers table first
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->date('join_date');
            $table->timestamps();
        });

        // 2. Create payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->timestamps();
        });

        // 3. Create transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->string('type');
            $table->integer('quantity');
            $table->decimal('delivery_cost', 15, 2)->default(0);
            $table->decimal('tax_cost', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->foreignId('payment_id')->constrained();
            $table->date('date');
            $table->timestamps();
        });

        // 4. Create warranties table
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->date('end_date');
            $table->string('condition');
            $table->timestamps();
        });

        // 5. Create services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->string('phone_type');
            $table->decimal('cost', 15, 2);
            $table->text('description');
            $table->string('status');
            $table->timestamps();
        });

        // 6. Create trade_ins table
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('old_phone');
            $table->decimal('old_value', 15, 2);
            $table->foreignId('new_product_id')->constrained('products');
            $table->date('date');
            $table->timestamps();
        });

        // 7. Update customers table with relation IDs
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('tradein_id')->nullable()->constrained('trade_ins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop tables in reverse order
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['tradein_id']);
            $table->dropColumn(['transaction_id', 'service_id', 'tradein_id']);
        });
        Schema::dropIfExists('trade_ins');
        Schema::dropIfExists('services');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('customers');
    }
};
