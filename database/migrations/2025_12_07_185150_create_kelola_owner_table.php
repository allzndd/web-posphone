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
        Schema::create('kelola_owner', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->string('nama_pemilik');
            $table->string('email')->unique();
            $table->string('telepon');
            $table->string('paket');
            $table->integer('jumlah_outlet')->default(1);
            $table->date('tanggal_daftar');
            $table->date('tanggal_expired');
            $table->enum('status', ['Active', 'Expired'])->default('Active');
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
        Schema::dropIfExists('kelola_owner');
    }
};
