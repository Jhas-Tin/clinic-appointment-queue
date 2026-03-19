<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    // Fillable fields for mass assignment
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'availability_status',
    ];

    /**
     * Get the doctor that owns this schedule
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Optional: Cast start_time and end_time to time format
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
}