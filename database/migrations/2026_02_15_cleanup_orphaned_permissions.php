<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete package_permissions that reference non-existent permissions
        DB::statement('
            DELETE FROM package_permissions 
            WHERE permissions_id NOT IN (SELECT id FROM permissions)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration as we deleted data
    }
};
