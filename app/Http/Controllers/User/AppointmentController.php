<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // Show all appointments for logged-in user
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
                                   ->latest()
                                   ->get();

        return view('user.appointments', compact('appointments'));
    }

    // Store a new appointment
    public function store(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'patient_name' => Auth::user()->name,
            'doctor_name' => $request->doctor_name,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Appointment booked successfully!');
    }
}
