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

        // Fetch all doctors (status and schedule included)
        $doctors = Doctor::all();

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
            'date' => 'required|date',
            'time' => 'required',
            'email' => 'required|email',
            'emergency_contact' => 'nullable',
            'parent_guardian' => 'nullable',
        ]);

        // Fetch doctor
        $doctor = Doctor::where('name', $request->doctor_name)->first();

        if (!$doctor) {
            return back()->withErrors(['doctor_name' => 'Selected doctor does not exist.']);
        }

        // Check doctor status
        if ($doctor->status !== 'Available') {
            return back()->withErrors(['doctor_name' => 'Doctor is currently unavailable.']);
        }

        // Check if doctor has schedule on the requested date
        if (!$doctor->available_date || $doctor->available_date->format('Y-m-d') != $request->date) {
            return back()->withErrors(['date' => 'Doctor is not available on this date.']);
        }

        // Check appointment time against doctor's schedule
        $appointmentTime = Carbon::createFromFormat('H:i', $request->time);
        $startTime = Carbon::createFromFormat('H:i', substr($doctor->start_time, 0, 5));
        $endTime = Carbon::createFromFormat('H:i', substr($doctor->end_time, 0, 5));

        if ($appointmentTime->lt($startTime) || $appointmentTime->gt($endTime)) {
            return back()->withErrors(['time' => 'Appointment time is outside doctor\'s available hours.']);
        }

        // Check if doctor already has an appointment at the same date and time
        $exists = Appointment::where('doctor_name', $request->doctor_name)
            ->where('date', $request->date)
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
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Appointment booked successfully.');
    }
}