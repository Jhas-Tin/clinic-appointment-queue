<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'available_date',
        'start_time',
        'end_time',
        'availability_status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'available_date' => 'date',
    ];

    /**
     * Get all schedules for this doctor
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Alias for schedules() to fix RelationNotFoundException
     */
    public function weeklySchedule(): HasMany
    {
        return $this->schedules();
    }

    /**
     * Optional helper: get today's schedule
     */
    public function todaySchedule()
    {
        $today = now()->format('l'); // e.g., 'Monday'
        return $this->schedules()->where('day_of_week', $today)->first();
    }
}