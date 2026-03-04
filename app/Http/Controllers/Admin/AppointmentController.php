<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;

class AppointmentController extends Controller
{

    public function index()
    {
        $appointments = Appointment::latest()->get();
        return view('admin.appointments', compact('appointments'));
    }


    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'Approved']);
        return back()->with('success', 'Appointment approved successfully');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'Cancelled']);
        return back()->with('success', 'Appointment cancelled successfully');
    }

    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.appointments-show', compact('appointment'));
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('admin.appointments')->with('success', 'Appointment deleted successfully.');
    }
}