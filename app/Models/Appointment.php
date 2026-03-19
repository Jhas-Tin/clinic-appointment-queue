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
        'email',
        'parent_guardian',
        'emergency_contact',
        'doctor_name',
        'date',
        'time',
        'status',
        'cancel_reason',
        'diagnosis',        // new field
        'prescription',     // new field
        'patient_status',   // new field for patient status (Go Home / Stay)
    ];

    protected $attributes = [
        'status' => 'Pending',
        'patient_status' => null, // default value can be null
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}