<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\AppointmentApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentCancelledMail;

class DashboardController extends Controller
{
    /**
     * Show doctor dashboard with appointments and counts
     */
    public function index()
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            return redirect()->route('login');
        }

        // get appointments of this doctor
        $appointments = Appointment::where('doctor_name', $doctor->name)
            ->latest()
            ->get();

        // counts
        $total = Appointment::where('doctor_name', $doctor->name)->count();
        $pending = Appointment::where('doctor_name', $doctor->name)
            ->where('status', 'Pending')
            ->count();
        $approved = Appointment::where('doctor_name', $doctor->name)
            ->where('status', 'Approved')
            ->count();
        $cancelled = Appointment::where('doctor_name', $doctor->name)
            ->where('status', 'Cancelled')
            ->count();

        return view('doctor.dashboard', compact(
            'appointments',
            'total',
            'pending',
            'approved',
            'cancelled'
        ));
    }

//     public function approve($id)
// {
//     $doctor = Auth::guard('doctor')->user();

//     $appointment = Appointment::where('id', $id)
//         ->where('doctor_name', $doctor->name)
//         ->firstOrFail();

//     $appointment->update(['status' => 'Approved']);

//     return back()->with('success', 'Appointment approved successfully.');
// }
public function approve($id)
{
    // Get logged-in doctor
    $doctor = Auth::guard('doctor')->user();

    // Find the appointment for this doctor
    $appointment = Appointment::where('id', $id)
        ->where('doctor_name', $doctor->name)
        ->firstOrFail();

    // Update status to Approved
    $appointment->update(['status' => 'Approved']);

    // Send email notification if patient email exists
    if ($appointment->email) {
        try {
            Mail::to($appointment->email)->send(new AppointmentApprovedMail($appointment));
        } catch (\Exception $e) {
            // Log the error but don't break the request
            Log::error('Failed to send approval email: ' . $e->getMessage());
            return back()->with('success', 'Appointment approved, but email could not be sent.');
        }
    }

    return back()->with('success', 'Appointment approved & email sent.');
}
public function cancel(Request $request, $id)
{
    $doctor = Auth::guard('doctor')->user();

    $request->validate([
        'cancel_reason' => 'required|string|max:255',
    ]);

    $appointment = Appointment::where('id', $id)
        ->where('doctor_name', $doctor->name)
        ->firstOrFail();

    $appointment->update([
        'status' => 'Cancelled',
        'cancel_reason' => $request->cancel_reason,
    ]);

    // ✅ SEND EMAIL
    Mail::to($appointment->email)
        ->send(new AppointmentCancelledMail($appointment));

    return back()->with('success', 'Appointment cancelled and email sent.');
}


public function destroy($id)
{
    $doctor = Auth::guard('doctor')->user();

    $appointment = Appointment::where('id', $id)
        ->where('doctor_name', $doctor->name)
        ->firstOrFail();

    $appointment->delete();

    return back()->with('success', 'Appointment deleted successfully.');
}

    /**
     * Show doctor profile page
     */
    public function profile()
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            return redirect()->route('login');
        }

        return view('doctor.profile', compact('doctor'));
    }

    public function availability()
{
    $doctor = Auth::guard('doctor')->user();

    if (!$doctor) {
        return redirect()->route('login');
    }

    return view('doctor.availability', compact('doctor'));
}

public function updateAvailability(Request $request)
{
    /** @var \App\Models\Doctor $doctor */
    $doctor = Auth::guard('doctor')->user();

    if (!$doctor) {
        return redirect()->route('login');
    }

    $request->validate([
        'available_date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required',
        'availability_status' => 'required',
    ]);

    $doctor->update([
        'available_date' => $request->available_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'availability_status' => $request->availability_status,
    ]);

    return back()->with('success', 'Availability updated');
}

    /**
     * Update doctor profile
     */
    public function updateProfile(Request $request)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            return redirect()->route('login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $doctor->name = $request->name;
        $doctor->email = $request->email;

        if ($request->password) {
            $doctor->password = Hash::make($request->password);
        }

        $doctor->save(); // ✅ Intelephense now recognizes this method

        return back()->with('success', 'Profile updated successfully!');
    }
}