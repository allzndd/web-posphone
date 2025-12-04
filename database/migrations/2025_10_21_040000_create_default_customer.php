<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create default 'Umum' customer for walk-in transactions
        DB::table('customers')->insert([
            'name' => 'Umum',
            'email' => 'umum@default.local',
            'phone' => '-',
            'address' => '-',
            'join_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('customers')->where('email', 'umum@default.local')->delete();
    }
};
