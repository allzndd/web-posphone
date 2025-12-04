<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add name if missing
        if (!Schema::hasColumn('services', 'name')) {
            Schema::table('services', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
            });
            // Try to migrate data from phone_type if exists
            if (Schema::hasColumn('services', 'phone_type')) {
                DB::table('services')->whereNull('name')->update([
                    'name' => DB::raw('phone_type')
                ]);
            }
        }

        // Add price if missing
        if (!Schema::hasColumn('services', 'price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            });
            // Try to migrate data from cost if exists
            if (Schema::hasColumn('services', 'cost')) {
                DB::table('services')->whereNull('price')->update([
                    'price' => DB::raw('cost')
                ]);
            }
        }

        // Add duration if missing
        if (!Schema::hasColumn('services', 'duration')) {
            Schema::table('services', function (Blueprint $table) {
                $table->integer('duration')->nullable()->after('price');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('services', 'duration')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('duration');
            });
        }
        if (Schema::hasColumn('services', 'price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
        if (Schema::hasColumn('services', 'name')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
};
