<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Doctor extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'available_date',
        'start_time',
        'end_time',
        'availability_status'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'available_date' => 'date',
    ];
}