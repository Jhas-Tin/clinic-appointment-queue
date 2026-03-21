<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_name',
        'email',
        'parent_guardian',
        'emergency_contact',
        'doctor_name',
        'facility_name',        // Added facility_name field
        'facility_id',          // Added facility_id field for reliable lookup
        'date',
        'time',
        'status',
        'cancel_reason',
        'diagnosis',            // new field
        'prescription',         // new field
        'medicine_quantity',    // Added medicine_quantity field
        'patient_status',       // new field for patient status (Go Home / Stay)
        'email_sent',           // Optional: track if email was sent
        'email_sent_at',        // Optional: when email was sent
    ];

    protected $attributes = [
        'status' => 'Pending',
        'patient_status' => null, // default value can be null
        'email_sent' => false,    // default email not sent
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
        'date' => 'date',
        // 'time' => 'datetime', // REMOVED - time should be stored as string
        'facility_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_name', 'name');
    }

    /**
     * Get clean time (extract only time part if it contains date)
     */
    public function getCleanTimeAttribute()
    {
        $timeValue = $this->time;
        
        if (!$timeValue) {
            return null;
        }
        
        // If time contains a space, it might be a full datetime string
        if (str_contains($timeValue, ' ')) {
            $timeParts = explode(' ', $timeValue);
            return end($timeParts);
        }
        
        return $timeValue;
    }

    /**
     * Get formatted time (12-hour format with AM/PM)
     */
    public function getFormattedTimeAttribute()
    {
        $cleanTime = $this->clean_time;
        
        if (!$cleanTime) {
            return null;
        }
        
        try {
            return Carbon::parse($cleanTime)->format('h:i A');
        } catch (\Exception $e) {
            return $cleanTime;
        }
    }

    /**
     * Get full datetime for appointment
     */
    public function getDateTimeAttribute()
    {
        try {
            $cleanTime = $this->clean_time;
            if ($cleanTime) {
                return Carbon::parse($this->date . ' ' . $cleanTime, 'Asia/Manila');
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if appointment is ongoing
     */
    public function isOngoing()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) {
            return false;
        }
        
        $now = Carbon::now('Asia/Manila');
        $endTime = $dateTime->copy()->addHour();
        
        return $now->between($dateTime, $endTime);
    }

    /**
     * Check if appointment is upcoming
     */
    public function isUpcoming()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) {
            return false;
        }
        
        $now = Carbon::now('Asia/Manila');
        
        return $dateTime->greaterThan($now);
    }

    /**
     * Check if appointment is completed
     */
    public function isCompleted()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) {
            return false;
        }
        
        $now = Carbon::now('Asia/Manila');
        $endTime = $dateTime->copy()->addHour();
        
        return $endTime->lessThan($now);
    }

    /**
     * Get dynamic status for display
     */
    public function getDynamicStatusAttribute()
    {
        if ($this->status === 'Cancelled') {
            return 'Cancelled';
        }
        
        if ($this->isOngoing()) {
            return 'Ongoing';
        }
        
        if ($this->isUpcoming()) {
            return 'Upcoming';
        }
        
        if ($this->isCompleted()) {
            return 'Completed';
        }
        
        return $this->status;
    }

    /**
     * Get facility details from campus database
     */
    public function getFacilityAttribute()
    {
        if (!$this->facility_id && !$this->facility_name) {
            return null;
        }

        try {
            // Try to get by ID first (more reliable)
            if ($this->facility_id) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('id', $this->facility_id)
                    ->first();
                
                if ($facility) {
                    return $facility;
                }
            }
            
            // Fallback to name lookup if ID not found
            if ($this->facility_name) {
                $facility = DB::connection('campus')
                    ->table('facilities')
                    ->where('name', $this->facility_name)
                    ->first();
                
                return $facility;
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper method to check if appointment can be approved
     */
    public function canBeApproved()
    {
        if ($this->status !== 'Pending') {
            return false;
        }

        // If appointment has a facility, check if facility is approved
        if ($this->facility_id || $this->facility_name) {
            try {
                $query = DB::connection('campus')->table('facilities');
                
                // Try by ID first
                if ($this->facility_id) {
                    $facility = $query->where('id', $this->facility_id)
                        ->where('status', 'active')
                        ->where('approval_status', 'accept')
                        ->first();
                } else {
                    // Fallback to name lookup
                    $facility = $query->where('name', $this->facility_name)
                        ->where('status', 'active')
                        ->where('approval_status', 'accept')
                        ->first();
                }
                
                return $facility !== null;
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * Helper method to get facility approval status
     */
    public function getFacilityApprovalStatus()
    {
        if (!$this->facility_id && !$this->facility_name) {
            return null;
        }

        try {
            $query = DB::connection('campus')->table('facilities');
            
            // Try by ID first
            if ($this->facility_id) {
                $facility = $query->where('id', $this->facility_id)->first();
            } else {
                // Fallback to name lookup
                $facility = $query->where('name', $this->facility_name)->first();
            }
            
            if (!$facility) {
                return 'not_found';
            }
            
            return $facility->approval_status ?? 'unknown';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    /**
     * Helper method to get facility approval text with badge
     */
    public function getFacilityApprovalText()
    {
        $status = $this->getFacilityApprovalStatus();
        
        switch ($status) {
            case 'accept':
                return ['text' => '✓ Facility Approved', 'class' => 'text-green-600 bg-green-50'];
            case 'pending':
                return ['text' => '⏳ Waiting for Facility Approval', 'class' => 'text-yellow-600 bg-yellow-50'];
            case 'decline':
                return ['text' => '✗ Facility Declined', 'class' => 'text-red-600 bg-red-50'];
            case 'not_found':
                return ['text' => '✗ Facility Not Found', 'class' => 'text-red-600 bg-red-50'];
            default:
                return ['text' => '⚠️ Facility Status Unknown', 'class' => 'text-gray-600 bg-gray-50'];
        }
    }

    /**
     * Get facility name with approval status badge HTML
     */
    public function getFacilityDisplayHtml()
    {
        if (!$this->facility_name && !$this->facility_id) {
            return '<span class="text-gray-400">—</span>';
        }
        
        $facility = $this->facility;
        $statusText = '';
        $statusClass = '';
        
        if ($facility) {
            if ($facility->approval_status === 'accept') {
                $statusText = '✓ Approved';
                $statusClass = 'text-green-600';
            } elseif ($facility->approval_status === 'pending') {
                $statusText = '⏳ Pending';
                $statusClass = 'text-yellow-600';
            } elseif ($facility->approval_status === 'decline') {
                $statusText = '✗ Declined';
                $statusClass = 'text-red-600';
            }
        } else {
            $statusText = '✗ Not Found';
            $statusClass = 'text-red-600';
        }
        
        return '<div class="flex flex-col">
                    <span class="text-gray-700">' . e($this->facility_name) . '</span>
                    <span class="text-xs ' . $statusClass . '">' . $statusText . '</span>
                </div>';
    }
}