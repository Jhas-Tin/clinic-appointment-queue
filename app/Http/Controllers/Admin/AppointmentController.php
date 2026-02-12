<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Show all appointments
     */
    public function index()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments', compact('appointments'));
    }

    /**
     * Approve appointment
     */
    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'Approved']);
        return back()->with('success', 'Appointment approved successfully');
    }

    /**
     * Cancel appointment
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'Cancelled']);
        return back()->with('success', 'Appointment cancelled successfully');
    }

    /**
     * View single appointment
     */
    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.appointments-show', compact('appointment'));
    }
}
