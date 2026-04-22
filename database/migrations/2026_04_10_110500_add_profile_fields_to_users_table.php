<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('dob');
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('address');
            }

            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('department');
            }

            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->after('designation');
            }

            if (!Schema::hasColumn('users', 'basic_salary')) {
                $table->decimal('basic_salary', 12, 2)->nullable()->after('employee_id');
            }

            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('basic_salary');
            }

            if (!Schema::hasColumn('users', 'resume')) {
                $table->string('resume')->nullable()->after('photo');
            }

            if (!Schema::hasColumn('users', 'aadhaar')) {
                $table->string('aadhaar')->nullable()->after('resume');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'dob',
                'address',
                'department',
                'designation',
                'employee_id',
                'basic_salary',
                'photo',
                'resume',
                'aadhaar',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
