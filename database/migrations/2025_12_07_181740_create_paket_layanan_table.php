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
        Schema::create('paket_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi');
            $table->decimal('harga', 15, 2);
            $table->string('durasi');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
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
        Schema::dropIfExists('paket_layanan');
    }
};
