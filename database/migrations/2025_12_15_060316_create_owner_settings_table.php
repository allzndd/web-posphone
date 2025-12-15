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
        Schema::create('owner_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->unique();
            $table->string('currency', 3)->default('IDR'); // IDR, MYR, USD
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
        Schema::dropIfExists('owner_settings');
    }
};
