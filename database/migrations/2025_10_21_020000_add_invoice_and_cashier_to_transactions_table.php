<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            if (!Schema::hasColumn('transactions', 'invoice_number')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->string('invoice_number')->nullable()->after('type');
                });
            }
            if (!Schema::hasColumn('transactions', 'notes')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->text('notes')->nullable()->after('total_price');
                });
            }
            if (!Schema::hasColumn('transactions', 'cashier_id')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete()->after('payment_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'cashier_id')) {
                    $table->dropConstrainedForeignId('cashier_id');
                }
                if (Schema::hasColumn('transactions', 'notes')) {
                    $table->dropColumn('notes');
                }
                if (Schema::hasColumn('transactions', 'invoice_number')) {
                    $table->dropColumn('invoice_number');
                }
            });
        }
    }
};
