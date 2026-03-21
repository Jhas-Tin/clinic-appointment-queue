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
use Illuminate\Support\Facades\DB;

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

        // Get facilities from campus database for approval status checking
        // Key by ID for direct lookup
        $facilities = DB::connection('campus')
            ->table('facilities')
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

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
            'cancelled',
            'facilities'
        ));
    }

    /**
     * Approve appointment - checks facility approval status first
     */
    public function approve($id)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        $appointment = Appointment::where('id', $id)
            ->where('doctor_name', $doctor->name)
            ->firstOrFail();

        // Check if appointment has a facility
        if ($appointment->facility_id || $appointment->facility_name) {
            // Try to find facility by ID first (more reliable)
            $facility = null;
            
            if ($appointment->facility_id) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('id', $appointment->facility_id)
                    ->where('status', 'active')
                    ->first();
            }
            
            // Fallback to name lookup if ID not found
            if (!$facility && $appointment->facility_name) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('name', $appointment->facility_name)
                    ->where('status', 'active')
                    ->first();
            }

            if (!$facility) {
                return back()->with('error', 'Cannot approve: The facility "' . ($appointment->facility_name ?? 'Unknown') . '" is not available.');
            }

            // Check if facility is approved
            if ($facility->approval_status !== 'accept') {
                $statusMessage = $facility->approval_status === 'pending' ? 'waiting for approval' : 'declined';
                return back()->with('error', 'Cannot approve: The facility "' . $facility->name . '" is ' . $statusMessage . '. Please wait for facility approval first.');
            }
        }

        // Simple approval without any form data
        $appointment->update([
            'status' => 'Approved',
        ]);

        return back()->with('success', 'Appointment approved successfully! You can send the consultation summary after the consultation ends.');
    }

    /**
     * Send consultation summary email after the consultation is done
     * This method saves diagnosis, prescription, and updates inventory
     */
    public function sendEmail(Request $request, $id)
    {
        /** @var Doctor|null $doctor */
        $doctor = Auth::guard('doctor')->user();

        $appointment = Appointment::where('id', $id)
            ->where('doctor_name', $doctor->name)
            ->firstOrFail();

        // Check if appointment is approved
        if ($appointment->status !== 'Approved') {
            return back()->with('error', 'Cannot send email. Appointment must be approved first.');
        }

        // Validate the form data
        $request->validate([
            'diagnosis' => 'required|string|max:500',
            'medicine_id' => 'nullable|exists:inventories,id',
            'medicine_quantity' => 'nullable|integer|min:1',
            'patient_status' => 'required|in:Go Home,Stay',
        ]);

        // Prepare prescription text and handle inventory deduction
        $prescriptionText = null;
        $medicineQuantity = null;

        if ($request->medicine_id) {
            $medicine = Inventory::find($request->medicine_id);

            if ($medicine) {
                // Store medicine name as prescription
                $prescriptionText = $medicine->name;
                $medicineQuantity = $request->medicine_quantity;

                // Deduct inventory if quantity is provided
                if ($request->medicine_quantity) {
                    if ($medicine->quantity < $request->medicine_quantity) {
                        return back()->with('error', 'Not enough stock for the prescribed medicine. Available stock: ' . $medicine->quantity);
                    }
                    $medicine->quantity -= $request->medicine_quantity;
                    $medicine->save();
                }
            }
        }

        // Update appointment with diagnosis, prescription, and patient status
        $appointment->update([
            'diagnosis' => $request->diagnosis,
            'prescription' => $prescriptionText,
            'medicine_quantity' => $medicineQuantity,
            'patient_status' => $request->patient_status,
        ]);

        // Send email
        if ($appointment->email) {
            try {
                Mail::to($appointment->email)->send(new AppointmentApprovedMail($appointment));
                
                $message = 'Consultation summary email sent successfully to ' . $appointment->email;
                if ($prescriptionText) {
                    $message .= ' and ' . $medicineQuantity . ' ' . $prescriptionText . ' deducted from inventory.';
                }
                
                return back()->with('success', $message);
            } catch (\Exception $e) {
                Log::error('Failed to send email: ' . $e->getMessage());
                return back()->with('error', 'Failed to send email. Error: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'No email address found for this appointment.');
    }

    /**
     * Cancel appointment with reason
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

        // Optionally send cancellation email
        if ($appointment->email) {
            try {
                Mail::to($appointment->email)->send(new AppointmentCancelledMail($appointment));
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email: ' . $e->getMessage());
            }
        }

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

        // If appointment had medicine prescribed, restore the stock
        if ($appointment->prescription && $appointment->medicine_quantity) {
            $medicine = Inventory::where('name', $appointment->prescription)->first();
            if ($medicine) {
                $medicine->quantity += $appointment->medicine_quantity;
                $medicine->save();
            }
        }

        $appointment->delete();

        return back()->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Get facility details by ID or name
     */
    private function getFacility($appointment)
    {
        try {
            // Try by ID first
            if ($appointment->facility_id) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('id', $appointment->facility_id)
                    ->first();
                
                if ($facility) {
                    return $facility;
                }
            }
            
            // Fallback to name lookup
            if ($appointment->facility_name) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('name', $appointment->facility_name)
                    ->first();
                
                return $facility;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to fetch facility: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if facility is approved
     */
    private function isFacilityApproved($appointment)
    {
        $facility = $this->getFacility($appointment);
        
        if (!$facility) {
            return false;
        }
        
        return $facility->approval_status === 'accept';
    }

    /**
     * Get facility approval status text
     */
    public function getFacilityStatus($facilityId = null, $facilityName = null)
    {
        try {
            $query = DB::connection('campus')->table('facilities');
            
            if ($facilityId) {
                $facility = $query->where('id', $facilityId)->first();
            } elseif ($facilityName) {
                $facility = $query->where('name', $facilityName)->first();
            } else {
                return null;
            }
            
            if (!$facility) {
                return ['status' => 'not_found', 'text' => 'Not Found', 'class' => 'text-red-600'];
            }
            
            switch ($facility->approval_status) {
                case 'accept':
                    return ['status' => 'accept', 'text' => 'Approved', 'class' => 'text-green-600'];
                case 'pending':
                    return ['status' => 'pending', 'text' => 'Pending Approval', 'class' => 'text-yellow-600'];
                case 'decline':
                    return ['status' => 'decline', 'text' => 'Declined', 'class' => 'text-red-600'];
                default:
                    return ['status' => 'unknown', 'text' => 'Unknown', 'class' => 'text-gray-600'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'text' => 'Error', 'class' => 'text-gray-600'];
        }
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
     * Update only doctor's online/offline status (like Discord)
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