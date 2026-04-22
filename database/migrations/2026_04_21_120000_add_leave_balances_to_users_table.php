<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'casual_leave_balance')) {
                $table->decimal('casual_leave_balance', 5, 1)->default(12)->after('basic_salary');
            }

            if (!Schema::hasColumn('users', 'sick_leave_balance')) {
                $table->decimal('sick_leave_balance', 5, 1)->default(8)->after('casual_leave_balance');
            }

            if (!Schema::hasColumn('users', 'paid_leave_balance')) {
                $table->decimal('paid_leave_balance', 5, 1)->default(10)->after('sick_leave_balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['casual_leave_balance', 'sick_leave_balance', 'paid_leave_balance'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
