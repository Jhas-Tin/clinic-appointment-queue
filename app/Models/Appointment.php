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
        'facility_name',
        'facility_id',
        'date',
        'time',
        'status',
        'cancel_reason',
        'diagnosis',
        'prescription',
        'medicine_quantity',
        'patient_status',
        'email_sent',
        'email_sent_at',
    ];

    protected static function booted()
    {
        static::created(function ($appointment) {
            if ($appointment->facility_id || $appointment->facility_name) {
                try {
                    DB::connection('campus')->table('reservations')->insert([
                        'user_id'                => $appointment->user_id,
                        'facility_id'            => $appointment->facility_id,
                        'description'            => "Clinic Sync: " . ($appointment->diagnosis ?? 'Medical Appointment'),
                        'requested_date'         => $appointment->date,
                        'guest_name'             => "CLINIC: " . $appointment->patient_name,
                        'guest_contact'          => $appointment->email,
                        'status'                 => 'pending',
                        'estimated_participants' => $appointment->medicine_quantity ?? 1,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Campus Reservation Sync Failed: " . $e->getMessage());
                }
            }
        });
    }

    protected $attributes = [
        'status' => 'Pending',
        'patient_status' => null,
        'email_sent' => false,
    ];

    protected $casts = [
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
        'date' => 'date',
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

    public function getCleanTimeAttribute()
    {
        $timeValue = $this->time;
        if (!$timeValue) return null;
        if (str_contains($timeValue, ' ')) {
            $timeParts = explode(' ', $timeValue);
            return end($timeParts);
        }
        return $timeValue;
    }

    public function getFormattedTimeAttribute()
    {
        $cleanTime = $this->clean_time;
        if (!$cleanTime) return null;
        try {
            return Carbon::parse($cleanTime)->format('h:i A');
        } catch (\Exception $e) {
            return $cleanTime;
        }
    }

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

    public function isOngoing()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) return false;
        $now = Carbon::now('Asia/Manila');
        $endTime = $dateTime->copy()->addHour();
        return $now->between($dateTime, $endTime);
    }

    public function isUpcoming()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) return false;
        $now = Carbon::now('Asia/Manila');
        return $dateTime->greaterThan($now);
    }

    public function isCompleted()
    {
        $dateTime = $this->date_time;
        if (!$dateTime) return false;
        $now = Carbon::now('Asia/Manila');
        $endTime = $dateTime->copy()->addHour();
        return $endTime->lessThan($now);
    }

    /**
     * UPDATED: Prioritize rejection check with broader string matching
     */
    public function getDynamicStatusAttribute()
    {
        if ($this->status === 'Cancelled') {
            return 'Cancelled';
        }

        $resStatus = $this->getFacilityApprovalStatus();
        
        // Comprehensive check for rejection variants
        if (in_array($resStatus, ['decline', 'declined', 'rejected', 'denied'])) {
            return 'Rejected';
        }
        
        // Only proceed to timing-based statuses if not rejected
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

    public function getFacilityAttribute()
    {
        if (!$this->facility_id && !$this->facility_name) return null;
        try {
            if ($this->facility_id) {
                return DB::connection('campus')->table('facilities')->where('id', $this->facility_id)->first();
            }
            return DB::connection('campus')->table('facilities')->where('name', $this->facility_name)->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function canBeApproved()
    {
        if ($this->status !== 'Pending') return false;

        if ($this->facility_id) {
            $status = $this->getFacilityApprovalStatus();
            return in_array($status, ['accept', 'accepted', 'approved']);
        }

        return true;
    }

    /**
     * UPDATED: Using whereDate and trim() for more accurate record retrieval
     */
    public function getFacilityApprovalStatus()
    {
        if (!$this->facility_id) return null;

        try {
            $reservation = DB::connection('campus')->table('reservations')
                ->where('facility_id', $this->facility_id)
                ->where('user_id', $this->user_id)
                ->whereDate('requested_date', $this->date) 
                ->where('guest_name', 'LIKE', 'CLINIC:%')
                ->first();
            
            return $reservation ? strtolower(trim($reservation->status)) : 'not_found';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function getFacilityApprovalText()
    {
        $status = $this->getFacilityApprovalStatus();
        switch ($status) {
            case 'accept':
            case 'accepted':
            case 'approved':
                return ['text' => '✓ Facility Approved', 'class' => 'text-green-600 bg-green-50'];
            case 'pending':
                return ['text' => '⏳ Waiting for Facility Approval', 'class' => 'text-yellow-600 bg-yellow-50'];
            case 'decline':
            case 'declined':
            case 'rejected':
            case 'denied':
                return ['text' => '✗ Facility Rejected', 'class' => 'text-red-600 bg-red-50'];
            default:
                return ['text' => '⚠️ Facility Status Unknown', 'class' => 'text-gray-600 bg-gray-50'];
        }
    }

    public function getFacilityDisplayHtml()
    {
        if (!$this->facility_name && !$this->facility_id) {
            return '<span class="text-gray-400">—</span>';
        }
        
        $status = $this->getFacilityApprovalStatus();
        $statusText = '⏳ Pending';
        $statusClass = 'text-yellow-600';
        
        if (in_array($status, ['accept', 'accepted', 'approved'])) {
            $statusText = '✓ Approved';
            $statusClass = 'text-green-600';
        } elseif (in_array($status, ['decline', 'declined', 'rejected', 'denied'])) {
            $statusText = '✗ Rejected';
            $statusClass = 'text-red-600';
        } elseif ($status === 'not_found' || $status === 'error') {
            $statusText = '✗ Not Found';
            $statusClass = 'text-red-600';
        }
        
        return '<div class="flex flex-col">
                    <span class="text-gray-700">' . e($this->facility_name) . '</span>
                    <span class="text-xs ' . $statusClass . '">' . $statusText . '</span>
                </div>';
    }
}