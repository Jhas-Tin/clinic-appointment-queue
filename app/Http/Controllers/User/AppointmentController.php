<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Show the user's appointments and doctors
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
            ->latest()
            ->get();

        // Load doctors and their weekly schedules for frontend availability
        $doctors = Doctor::with('schedules')->get();

        return view('user.appointments', compact('appointments', 'doctors'));
    }

    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required',
            'doctor_name' => 'required',
            'time' => 'required',
            'email' => 'required|email',
            'emergency_contact' => 'nullable',
            'parent_guardian' => 'nullable',
        ]);

        // Fetch doctor by name
        $doctor = Doctor::where('name', $request->doctor_name)->first();
        if (!$doctor) {
            return back()->withErrors(['doctor_name' => 'Selected doctor does not exist.']);
        }

        // Check doctor general availability
        if ($doctor->availability_status !== 'Available') {
            return back()->withErrors(['doctor_name' => 'Doctor is currently unavailable at this time.']);
        }

        // Determine today's schedule
        $today = now()->format('l'); // e.g., Monday, Tuesday
        $schedule = $doctor->schedules()
            ->where('day_of_week', $today)
            ->where('availability_status', 'Available')
            ->first();

        if (!$schedule) {
            return back()->withErrors(['doctor_name' => 'Doctor is unavailable today.']);
        }

        try {
            // Parse times safely
            $appointmentTime = Carbon::parse($request->time); // user input time
            $startTime = isset($schedule->start_time) ? Carbon::parse($schedule->start_time) : null;
            $endTime = isset($schedule->end_time) ? Carbon::parse($schedule->end_time) : null;

            // Validate time range
            if ($startTime && $endTime && ($appointmentTime->lt($startTime) || $appointmentTime->gt($endTime))) {
                return back()->withErrors([
                    'time' => 'Appointment time is outside doctor\'s available hours.'
                ]);
            }

            // Prevent booking past time
            $selectedDateTime = now()->setTimeFrom($appointmentTime);
            if ($selectedDateTime->lt(now())) {
                return back()->withErrors(['time' => 'You cannot book an appointment in the past.']);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['time' => 'Invalid time format.']);
        }

        // Check duplicate appointment
        $exists = Appointment::where('doctor_name', $request->doctor_name)
            ->where('date', now()->format('Y-m-d')) // appointment is today
            ->where('time', $request->time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['time' => 'This doctor already has an appointment at this time.']);
        }

        // Create appointment
        Appointment::create([
            'user_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'email' => $request->email,
            'emergency_contact' => $request->emergency_contact,
            'parent_guardian' => $request->parent_guardian,
            'doctor_name' => $request->doctor_name,
            'date' => now()->format('Y-m-d'), // appointment date is today
            'time' => $request->time,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Appointment booked successfully.');
    }
}