<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                // Make join_date nullable with default
                if (Schema::hasColumn('customers', 'join_date')) {
                    $table->date('join_date')->nullable()->default(now())->change();
                }

                // Drop status column if exists
                if (Schema::hasColumn('customers', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'join_date')) {
                    $table->date('join_date')->nullable(false)->change();
                }

                $table->enum('status', ['active', 'inactive'])->default('active')->after('address');
            });
        }
    }
};
