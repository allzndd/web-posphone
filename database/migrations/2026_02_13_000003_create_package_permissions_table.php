<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('package_permissions', function (Blueprint $table) {
            $table->integer('id', true, true)->length(11); // int(11) AUTO_INCREMENT
            $table->integer('tipe_layanan_id')->length(11)->nullable(false);
            $table->integer('permissions_id')->length(11)->nullable(false);
            $table->integer('max_records')->length(11)->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_permissions');
    }
};
