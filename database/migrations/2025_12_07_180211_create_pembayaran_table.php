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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('owner_name');
            $table->string('email');
            $table->string('paket');
            $table->string('periode');
            $table->decimal('total', 15, 2);
            $table->enum('status', ['Paid', 'Pending', 'Failed'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
