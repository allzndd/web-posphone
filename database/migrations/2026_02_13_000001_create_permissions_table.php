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
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('id', true, true)->length(11); // int(11) AUTO_INCREMENT
            $table->string('nama', 150)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('modul', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('aksi', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
