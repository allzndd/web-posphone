<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_warna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_owner')->nullable()->comment('Owner ID, null if global');
            $table->string('warna', 100)->comment('Color code in HEX format');
            $table->boolean('is_global')->default(1)->comment('Is this color global for all owners');
            $table->timestamps();

            $table->foreign('id_owner')->references('id')->on('users')->onDelete('set null');
            $table->index('id_owner');
            $table->index('is_global');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_warna');
    }
};
