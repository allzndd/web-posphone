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
        // Update all STAFF and USER roles to ADMIN
        DB::table('users')
            ->whereIn('roles', ['STAFF', 'USER', 'staff', 'user'])
            ->update(['roles' => 'ADMIN']);

        // Update any 'admin' to 'ADMIN' (case normalization)
        DB::table('users')
            ->where('roles', 'admin')
            ->update(['roles' => 'ADMIN']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally revert back (though this might not be accurate)
        // Leave empty as we can't reliably determine original roles
    }
};
