<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorAvailabilityController extends Controller
{
    /**
     * Show list of all doctors with their schedules
     */
    public function index()
    {
        $doctors = Doctor::with('schedules')->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        return view('admin.doctor-availability', compact('doctors', 'days'));
    }

    /**
     * Show form to edit specific doctor's availability
     */
    public function edit($id)
    {
        $doctor = Doctor::with('schedules')->findOrFail($id);
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Create a map of existing schedules by day
        $weeklySchedule = [];
        foreach ($doctor->schedules as $schedule) {
            $weeklySchedule[$schedule->day_of_week] = $schedule;
        }
        
        return view('admin.doctor-availability', compact('doctor', 'days', 'weeklySchedule'));
    }

    /**
     * Update doctor's weekly availability
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);
        
        $request->validate([
            'days' => 'array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $selectedDays = $request->days ?? [];

        foreach ($days as $day) {
            $isAvailable = in_array($day, $selectedDays);

            DoctorSchedule::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day
                ],
                [
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'availability_status' => $isAvailable ? 'Available' : 'Unavailable',
                    'status' => $isAvailable ? 'Online' : 'Offline',
                ]
            );
        }

        // Update doctor's default times
        $doctor->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // UPDATED: Redirect back to edit page instead of index
        return redirect()->route('admin.doctor-availability.edit', $doctor->id)
            ->with('success', 'Weekly schedule updated successfully for ' . $doctor->name);
    }
}