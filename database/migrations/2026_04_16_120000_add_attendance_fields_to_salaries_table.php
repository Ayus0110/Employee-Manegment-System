<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'daily_rate')) {
                $table->decimal('daily_rate', 10, 2)->default(0)->after('month');
            }

            if (!Schema::hasColumn('salaries', 'present_days')) {
                $table->unsignedInteger('present_days')->default(0)->after('daily_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (Schema::hasColumn('salaries', 'present_days')) {
                $table->dropColumn('present_days');
            }

            if (Schema::hasColumn('salaries', 'daily_rate')) {
                $table->dropColumn('daily_rate');
            }
        });
    }
};
