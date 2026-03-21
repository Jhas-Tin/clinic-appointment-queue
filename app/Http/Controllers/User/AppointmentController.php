<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Get ALL facilities from campus database (pending, accept, decline)
        // Users can book all facilities regardless of approval status
        $facilities = DB::connection('campus')
            ->table('facilities')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('user.appointments', compact('appointments', 'doctors', 'facilities'));
    }

    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'doctor_name' => 'required|string',
            'time' => 'required',
            'email' => 'required|email',
            'emergency_contact' => 'nullable|string|max:20',
            'parent_guardian' => 'nullable|string|max:255',
            'facility_id' => 'nullable|exists:campus.facilities,id',
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

        // Check duplicate appointment for the same doctor
        $exists = Appointment::where('doctor_name', $request->doctor_name)
            ->where('date', now()->format('Y-m-d')) // appointment is today
            ->where('time', $request->time)
            ->exists();

        if ($exists) {
            return back()->withErrors(['time' => 'This doctor already has an appointment at this time.']);
        }

        // Get facility details if selected
        $facilityName = null;
        $facilityId = null;
        $facilityApprovalStatus = null;
        
        if ($request->facility_id) {
            $facility = DB::connection('campus')
                ->table('facilities')
                ->where('id', $request->facility_id)
                ->where('status', 'active')
                ->first();
                
            if (!$facility) {
                return back()->withErrors(['facility_id' => 'Selected facility is not available.']);
            }
            
            $facilityName = $facility->name;
            $facilityId = $facility->id;
            $facilityApprovalStatus = $facility->approval_status;
            
            // ONLY check for double booking if the facility is ACCEPTED
            if ($facilityApprovalStatus === 'accept') {
                $facilityBooked = Appointment::where('facility_id', $facilityId)
                    ->where('date', now()->format('Y-m-d'))
                    ->where('time', $request->time)
                    ->whereIn('status', ['Pending', 'Approved'])
                    ->exists();
                    
                if ($facilityBooked) {
                    return back()->withErrors(['facility_id' => 'This facility is already booked for the selected time. Please choose a different time or facility.']);
                }
            }
        }

        // Create appointment
        Appointment::create([
            'user_id' => Auth::id(),
            'patient_name' => $request->patient_name,
            'email' => $request->email,
            'emergency_contact' => $request->emergency_contact,
            'parent_guardian' => $request->parent_guardian,
            'doctor_name' => $request->doctor_name,
            'facility_name' => $facilityName,
            'facility_id' => $facilityId,
            'date' => now()->format('Y-m-d'), // appointment date is today
            'time' => $request->time,
            'status' => 'Pending',
        ]);

        $message = 'Appointment booked successfully.';
        if ($facilityApprovalStatus === 'pending') {
            $message .= ' Note: This facility is pending approval.';
        } elseif ($facilityApprovalStatus === 'decline') {
            $message .= ' Note: This facility is declined.';
        }
        
        return back()->with('success', $message);
    }

    /**
     * Helper method to get available facilities for booking
     * Returns all facilities (no restriction)
     */
    public function getAllFacilities()
    {
        return DB::connection('campus')
            ->table('facilities')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Helper method to check if a facility is available at a specific time
     * Only checks if facility is accepted (since pending/decline can be booked multiple times)
     */
    public function isFacilityAvailable($facilityId, $date, $time)
    {
        // First get facility to check its approval status
        $facility = DB::connection('campus')
            ->table('facilities')
            ->where('id', $facilityId)
            ->first();
            
        // If facility is not accepted, it's always available (no double booking restriction)
        if (!$facility || $facility->approval_status !== 'accept') {
            return true;
        }
        
        // Only check for accepted facilities
        $exists = Appointment::where('facility_id', $facilityId)
            ->where('date', $date)
            ->where('time', $time)
            ->whereIn('status', ['Pending', 'Approved'])
            ->exists();
            
        return !$exists;
    }
}