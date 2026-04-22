<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveModel extends Model
{
    protected $table = 'leaves';

    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'days',
        'type',
        'reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
