<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'head'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
