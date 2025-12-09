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
        Schema::create('pos_supplier', function (Blueprint $table) {
            $table->id();
            $table->integer('owner_id')->nullable();
            $table->string('nama')->nullable();
            $table->string('slug')->nullable();
            $table->string('nomor_hp', 45)->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('pos_supplier');
    }
};
