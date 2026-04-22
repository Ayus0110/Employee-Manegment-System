<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    protected $fillable = [
        'user_id',
        'employee_id',
        'department_id',
        'designation',
        'dob',
        'address',
        'basic_salary',
        'schedule_type',
        'shift_start',
        'shift_end',
    ];
}
