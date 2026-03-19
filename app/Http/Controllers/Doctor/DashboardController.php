<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\AppointmentApprovedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AppointmentCancelledMail;
use Carbon\Carbon;

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

        $appointments = Appointment::where('doctor_name', $doctor->name)
            ->latest()
            ->get();

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

    /**
     * Approve appointment, save patient sickness and prescribed medicine, update inventory
     */
    public function approve(Request $request, $id)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        $appointment = Appointment::where('id', $id)
            ->where('doctor_name', $doctor->name)
            ->firstOrFail();

        // Validate diagnosis, medicine info, and patient status
        $request->validate([
            'diagnosis' => 'required|string|max:500',
            'medicine_id' => 'nullable|exists:inventories,id',
            'medicine_quantity' => 'nullable|integer|min:1',
            'patient_status' => 'required|in:Go Home,Stay',
        ]);

        // Prepare prescription text
        $prescriptionText = null;

        if ($request->medicine_id) {
            $medicine = Inventory::find($request->medicine_id);

            if ($medicine) {
                // ONLY store medicine name
                $prescriptionText = $medicine->name;

                // Deduct inventory if quantity is provided
                if ($request->medicine_quantity) {
                    if ($medicine->quantity < $request->medicine_quantity) {
                        return back()->with('error', 'Not enough stock for the prescribed medicine.');
                    }
                    $medicine->quantity -= $request->medicine_quantity;
                    $medicine->save();
                }
            }
        }

        // Update appointment with diagnosis, prescription, and patient status
        $appointment->update([
            'status' => 'Approved',
            'diagnosis' => $request->diagnosis,
            'prescription' => $prescriptionText,
            'patient_status' => $request->patient_status,
        ]);

        // Send email to parent
        if ($appointment->email) {
            try {
                Mail::to($appointment->email)->send(new AppointmentApprovedMail($appointment));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
                return back()->with('success', 'Appointment approved, but email could not be sent.');
            }
        }

        return back()->with('success', 'Appointment approved, patient details saved, inventory updated, and patient status recorded.');
    }

    /**
     * Cancel appointment
     */
    public function cancel(Request $request, $id)
    {
        /** @var Doctor|null $doctor */
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

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Delete appointment
     */
    public function destroy($id)
    {
        /** @var Doctor|null $doctor */
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

    /**
     * Show doctor availability page with weekly schedule (VIEW ONLY)
     */
    public function availability()
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            return redirect()->route('login');
        }

        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        // Get weekly schedule for this doctor
        $weeklySchedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get()
            ->keyBy('day_of_week');

        return view('doctor.availability', compact('doctor', 'weeklySchedule', 'days'));
    }

    /**
     * NEW METHOD: Update only doctor's online/offline status (like Discord)
     */
    public function updateStatus(Request $request)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        if (!$doctor) {
            return redirect()->route('login');
        }

        $request->validate([
            'availability_status' => 'required|in:Available,Unavailable'
        ]);

        // Update the status in doctors table
        $doctor->update([
            'availability_status' => $request->availability_status
        ]);

        // Optional: Also update today's schedule status for real-time reflection
        $today = Carbon::now()->format('l');
        $todaySchedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $today)
            ->first();
        
        if ($todaySchedule) {
            $todaySchedule->update([
                'status' => $request->availability_status == 'Available' ? 'Online' : 'Offline'
            ]);
        }

        $statusText = $request->availability_status == 'Available' ? 'Online' : 'Offline';
        
        return back()->with('success', "Your status has been updated to {$statusText} successfully.");
    }

    /**
     * Update doctor profile
     */
    public function updateProfile(Request $request)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

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

        $doctor->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}