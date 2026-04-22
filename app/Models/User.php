<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'address',
        'department',
        'designation',
        'employee_id',
        'basic_salary',
        'casual_leave_balance',
        'sick_leave_balance',
        'paid_leave_balance',
        'role',
        'password',
        'force_password_change',
        'photo',
        'resume',
        'aadhaar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'force_password_change' => 'boolean',
        'basic_salary' => 'decimal:2',
        'casual_leave_balance' => 'decimal:1',
        'sick_leave_balance' => 'decimal:1',
        'paid_leave_balance' => 'decimal:1',
    ];

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }
}
