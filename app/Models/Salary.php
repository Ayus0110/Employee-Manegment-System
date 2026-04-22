<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    //
protected $fillable = [
    'user_id',
    'month',
    'daily_rate',
    'present_days',
    'basic_salary',
    'bonus',
    'deduction',
    'net_salary',
    'status'
];

protected $casts = [
    'daily_rate' => 'decimal:2',
    'present_days' => 'integer',
    'basic_salary' => 'decimal:2',
    'bonus' => 'decimal:2',
    'deduction' => 'decimal:2',
    'net_salary' => 'decimal:2',
];

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}
