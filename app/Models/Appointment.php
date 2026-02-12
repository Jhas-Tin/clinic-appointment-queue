<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_name',
        'doctor_name',
        'date',
        'time',
        'status'
    ];

    protected $attributes = [
        'status' => 'Pending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
