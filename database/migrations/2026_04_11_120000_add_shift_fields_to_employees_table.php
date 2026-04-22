<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'schedule_type')) {
                $table->string('schedule_type')->nullable()->after('basic_salary');
            }

            if (!Schema::hasColumn('employees', 'shift_start')) {
                $table->time('shift_start')->nullable()->after('schedule_type');
            }

            if (!Schema::hasColumn('employees', 'shift_end')) {
                $table->time('shift_end')->nullable()->after('shift_start');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [];

            foreach (['schedule_type', 'shift_start', 'shift_end'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
