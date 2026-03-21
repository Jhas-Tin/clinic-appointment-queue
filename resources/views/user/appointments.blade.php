@extends('layouts.user')

@section('title', 'My Appointments')

@section('content')
<div class="space-y-6">

    <!-- WELCOME SECTION -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">All Appointments</h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }}</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fa fa-calendar-check text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Appointments Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-calendar-check mr-2"></i>
                    Total Consultations
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $appointments->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time consultations</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-calendar text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-hourglass-half mr-2"></i>
                    Pending
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-yellow-600">{{ $appointments->where('status','Pending')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-check-circle mr-2"></i>
                    Approved
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-green-600">{{ $appointments->where('status','Approved')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Confirmed appointments</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-thumbs-up text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancelled Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-5 py-3">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fa fa-times-circle mr-2"></i>
                    Cancelled
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-red-600">{{ $appointments->where('status','Cancelled')->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Cancelled appointments</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-ban text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOOK BUTTON -->
    <div class="flex justify-end">
        <button onclick="document.getElementById('appointmentModal').classList.remove('hidden')" 
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200">
            <i class="fa fa-plus-circle"></i>
            Create Consultation
        </button>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-list mr-2"></i>
                    My Consultation History
                </h3>
                <div class="flex gap-2">
                    <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full flex items-center">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                        {{ $appointments->where('status','Approved')->count() }} Approved
                    </span>
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full flex items-center">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1"></span>
                        {{ $appointments->where('status','Pending')->count() }} Pending
                    </span>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Facility</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-semibold text-sm">{{ substr($appointment->patient_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                    <p class="text-xs text-gray-500">#APT-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fa fa-user-md text-purple-600 text-xs"></i>
                                </div>
                                <span class="text-gray-700">{{ $appointment->doctor_name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fa fa-building text-gray-400 mr-2"></i>
                                <span class="text-gray-700">{{ $appointment->facility_name ?? 'Not specified' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fa fa-calendar-alt text-gray-400 mr-2"></i>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class="fa fa-clock text-gray-400 mr-2"></i>
                                <span class="font-mono">{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'Approved' => 'bg-green-100 text-green-700 border-green-200',
                                    'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Cancelled' => 'bg-red-100 text-red-700 border-red-200',
                                ];
                                $statusIcons = [
                                    'Approved' => 'fa-check-circle',
                                    'Pending' => 'fa-hourglass-half',
                                    'Cancelled' => 'fa-times-circle',
                                ];
                                $color = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-700';
                                $icon = $statusIcons[$appointment->status] ?? 'fa-circle';
                            @endphp
                            
                            @if($appointment->status == 'Cancelled')
                                <button class="showReasonBtn px-3 py-1.5 rounded-full text-xs font-semibold flex items-center border {{ $color }} hover:shadow-md transition"
                                    data-reason="{{ $appointment->cancel_reason }}"
                                    data-patient="{{ $appointment->patient_name }}"
                                    data-email="{{ $appointment->email }}"
                                    data-emergency="{{ $appointment->emergency_contact }}"
                                    data-guardian="{{ $appointment->parent_guardian }}"
                                    data-doctor="{{ $appointment->doctor_name }}"
                                    data-date="{{ $appointment->date }}"
                                    data-time="{{ $appointment->time }}">
                                    <i class="fa {{ $icon }} mr-1.5"></i>
                                    {{ $appointment->status }}
                                    <i class="fa fa-eye ml-1.5 text-xs opacity-70"></i>
                                </button>
                            @else
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center w-fit border {{ $color }}">
                                    <i class="fa {{ $icon }} mr-1.5"></i>
                                    {{ $appointment->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center text-gray-400">
                                <i class="fa fa-calendar-times text-5xl mb-3"></i>
                                <p class="text-lg font-medium">No appointments found</p>
                                <p class="text-sm">Click the button above to create your first consultation</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($appointments->count() > 0)
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Showing <span class="font-medium">{{ $appointments->count() }}</span> appointments</span>
                <span>Last updated {{ \Carbon\Carbon::now()->format('h:i A') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- CANCEL REASON + REBOOK MODAL -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-96 p-6 relative animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fa fa-info-circle text-red-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Cancellation Reason</h3>
            <button onclick="closeReasonModal()" class="ml-auto text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
            <p id="reasonText" class="text-gray-800"></p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button onclick="closeReasonModal()" class="px-4 py-2 border-2 border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium">Close</button>
            <button id="rebookFromCancel" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200 flex items-center gap-2">
                <i class="fa fa-calendar-plus"></i>
                Rebook
            </button>
        </div>
    </div>
</div>

<!-- BOOKING MODAL WITH FACILITY SELECTION -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative animate-fade-in max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 sticky top-0 bg-white pb-2 border-b">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa fa-calendar-plus text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Book Consultation</h2>
            </div>
            <button onclick="document.getElementById('appointmentModal').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <form id="appointmentForm" action="{{ route('user.appointments.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-user text-blue-500 mr-1"></i>
                    Full Name
                </label>
                <input type="text" id="patientInput" name="patient_name" placeholder="Enter patient's full name" 
                       class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
            </div>

            <!-- Parent/Guardian -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-users text-green-500 mr-1"></i>
                    Parent/Guardian
                </label>
                <input type="text" id="guardianInput" name="parent_guardian" placeholder="Enter parent/guardian name" 
                       class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
            </div>

            <!-- Emergency Contact -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-phone-alt text-red-500 mr-1"></i>
                    Emergency Contact
                </label>
                <input type="text" id="emergencyInput" name="emergency_contact" placeholder="Enter emergency contact number" 
                       class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-envelope text-purple-500 mr-1"></i>
                    Parent Email Address
                </label>
                <input type="email" id="emailInput" name="email" placeholder="Enter parent email" 
                       class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
            </div>

            <!-- Facility Selection with Status Display -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-building text-indigo-500 mr-1"></i>
                    Select Facility (Optional)
                </label>
                <select id="facilitySelect" name="facility_id" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">-- No facility needed --</option>
                    @foreach($facilities as $facility)
                        @php
                            $statusBadge = '';
                            $statusClass = '';
                            $isAccepted = $facility->approval_status === 'accept';
                            
                            if ($facility->approval_status === 'accept') {
                                $statusBadge = '✓';
                                $statusClass = 'text-green-600';
                            } elseif ($facility->approval_status === 'pending') {
                                $statusBadge = '⏳';
                                $statusClass = 'text-yellow-600';
                            } elseif ($facility->approval_status === 'decline') {
                                $statusBadge = '✗';
                                $statusClass = 'text-red-600';
                            }
                        @endphp
                        <option value="{{ $facility->id }}" 
                                data-capacity="{{ $facility->capacity }}"
                                data-location="{{ $facility->location }}"
                                data-hours="{{ $facility->available_hours }}"
                                data-status="{{ $facility->approval_status }}"
                                data-accepted="{{ $isAccepted ? 'true' : 'false' }}"
                                class="{{ $statusClass }}">
                            {{ $facility->name }} 
                            @if($facility->location) - {{ $facility->location }} @endif
                            (Capacity: {{ $facility->capacity }}) 
                            <span class="text-xs {{ $statusClass }}">
                                {{ $statusBadge }} {{ ucfirst($facility->approval_status) }}
                            </span>
                        </option>
                    @endforeach
                </select>
                <div id="facilityInfo" class="mt-2 text-sm text-gray-600 bg-blue-50 p-2 rounded-lg hidden"></div>
                <div id="facilityWarning" class="mt-2 text-sm text-yellow-600 bg-yellow-50 p-2 rounded-lg hidden">
                    <i class="fa fa-exclamation-triangle mr-1"></i>
                    <span id="facilityWarningText"></span>
                </div>
            </div>

            <!-- Doctor Selection -->
            @php $today = now()->format('l'); @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-user-md text-indigo-500 mr-1"></i>
                    Select Doctor
                </label>
                <select id="doctorSelect" name="doctor_name" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                    <option value="">-- Choose a doctor --</option>
                    @foreach($doctors as $doctor)
                        @php
                            $todaySchedule = $doctor->schedules()->where('day_of_week', $today)->where('availability_status', 'Available')->first();
                            $isAvailable = $todaySchedule !== null;
                        @endphp
                        @if($isAvailable)
                            <option value="{{ $doctor->name }}"
                                data-date="{{ $todaySchedule->date ?? now()->format('Y-m-d') }}"
                                data-start="{{ $todaySchedule->start_time }}"
                                data-end="{{ $todaySchedule->end_time }}"
                                data-status="{{ $todaySchedule->availability_status }}">
                                {{ $doctor->name }} (Available Today)
                            </option>
                        @endif
                    @endforeach
                </select>
                <div id="doctorAvailability" class="mt-2 text-sm text-gray-600 bg-blue-50 p-2 rounded-lg hidden"></div>
            </div>

            <input type="hidden" id="dateInput" name="date">

            <!-- Time Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-clock text-yellow-500 mr-1"></i>
                    Preferred Time
                </label>
                <input type="time" id="timeInput" name="time" 
                       class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('appointmentModal').classList.add('hidden')" 
                        class="px-4 py-2 border-2 border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fa fa-calendar-check"></i>
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
</style>

<script>
// Facility selection info with double booking warning for accepted facilities
const facilitySelect = document.getElementById('facilitySelect');
const facilityInfo = document.getElementById('facilityInfo');
const facilityWarning = document.getElementById('facilityWarning');
const facilityWarningText = document.getElementById('facilityWarningText');
const timeInput = document.getElementById('timeInput');

function checkFacilityAvailability() {
    const selected = facilitySelect.selectedOptions[0];
    const time = timeInput.value;
    
    if (!selected || !selected.value || !time) {
        return;
    }
    
    const facilityId = selected.value;
    const facilityStatus = selected.dataset.status;
    const isAccepted = selected.dataset.accepted === 'true';
    
    // Only check for accepted facilities
    if (isAccepted && time) {
        // AJAX call to check if facility is already booked
        fetch(`/check-facility-availability?facility_id=${facilityId}&time=${time}`)
            .then(response => response.json())
            .then(data => {
                if (data.booked) {
                    facilityWarning.classList.remove('hidden');
                    facilityWarningText.innerHTML = `<i class="fa fa-clock mr-1"></i> This facility is already booked for ${time}. Please choose a different time.`;
                    facilityWarning.classList.add('bg-red-50', 'text-red-600');
                    facilityWarning.classList.remove('bg-yellow-50', 'text-yellow-600');
                } else {
                    facilityWarning.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error checking facility availability:', error);
            });
    } else {
        facilityWarning.classList.add('hidden');
    }
}

if (facilitySelect) {
    facilitySelect.addEventListener('change', function() {
        const selected = facilitySelect.selectedOptions[0];
        
        if (!selected || !selected.value) {
            facilityInfo.classList.add('hidden');
            facilityWarning.classList.add('hidden');
            return;
        }
        
        const capacity = selected.dataset.capacity;
        const location = selected.dataset.location;
        const hours = selected.dataset.hours;
        const facilityStatus = selected.dataset.status;
        const isAccepted = selected.dataset.accepted === 'true';
        
        let statusText = '';
        let statusColor = '';
        
        if (facilityStatus === 'accept') {
            statusText = '✓ Approved - This facility can be booked once per time slot';
            statusColor = 'text-green-600';
        } else if (facilityStatus === 'pending') {
            statusText = '⏳ Pending Approval - Multiple bookings allowed';
            statusColor = 'text-yellow-600';
        } else if (facilityStatus === 'decline') {
            statusText = '✗ Declined - Multiple bookings allowed';
            statusColor = 'text-red-600';
        }
        
        facilityInfo.innerHTML = `
            <i class="fa fa-info-circle mr-1"></i>
            <strong>Location:</strong> ${location || 'Not specified'} | 
            <strong>Capacity:</strong> ${capacity} persons | 
            <strong>Available Hours:</strong> ${hours || 'N/A'} hours
            <br><small class="${statusColor}">${statusText}</small>
        `;
        facilityInfo.classList.remove('hidden');
        facilityInfo.classList.add('bg-blue-50', 'text-blue-700');
        
        // Check availability if time is already selected
        if (timeInput && timeInput.value) {
            checkFacilityAvailability();
        }
    });
}

// Check availability when time changes
if (timeInput) {
    timeInput.addEventListener('change', function() {
        if (facilitySelect && facilitySelect.value) {
            checkFacilityAvailability();
        }
    });
}

// Show cancellation reason modal
let selectedBooking = {};
document.querySelectorAll('.showReasonBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        selectedBooking = {
            patient: this.dataset.patient,
            email: this.dataset.email,
            emergency: this.dataset.emergency,
            guardian: this.dataset.guardian,
            doctor: this.dataset.doctor,
            date: this.dataset.date,
            time: this.dataset.time
        };

        const reason = this.dataset.reason || "No reason provided";
        document.getElementById('reasonText').innerText = reason;

        const modal = document.getElementById('reasonModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});

// Rebook from cancellation modal
const rebookBtn = document.getElementById('rebookFromCancel');
if (rebookBtn) {
    rebookBtn.addEventListener('click', function() {
        closeReasonModal();
        
        const modal = document.getElementById('appointmentModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.getElementById('patientInput').value = selectedBooking.patient || '';
        document.getElementById('emailInput').value = selectedBooking.email || '';
        document.getElementById('emergencyInput').value = selectedBooking.emergency || '';
        document.getElementById('guardianInput').value = selectedBooking.guardian || '';

        const doctorSelect = document.getElementById('doctorSelect');
        if (doctorSelect) {
            for (let i = 0; i < doctorSelect.options.length; i++) {
                if (doctorSelect.options[i].value === selectedBooking.doctor) {
                    doctorSelect.options[i].selected = true;
                    doctorSelect.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }

        document.getElementById('timeInput').value = selectedBooking.time || '';
    });
}

// Doctor availability
const doctorSelect = document.getElementById('doctorSelect');
const doctorAvailability = document.getElementById('doctorAvailability');
const dateInput = document.getElementById('dateInput');

if (doctorSelect) {
    doctorSelect.addEventListener('change', function() {
        const selected = doctorSelect.selectedOptions[0];
        
        if (!selected || !selected.value) {
            if (doctorAvailability) doctorAvailability.classList.add('hidden');
            if (timeInput) {
                timeInput.value = '';
                timeInput.disabled = true;
            }
            return;
        }
        
        const date = selected.dataset.date;
        let start = selected.dataset.start;
        let end = selected.dataset.end;
        const status = selected.dataset.status;

        if (start && start.length > 5) start = start.substring(0,5);
        if (end && end.length > 5) end = end.substring(0,5);

        if (dateInput) dateInput.value = date;

        if(status !== 'Available') {
            if (doctorAvailability) {
                doctorAvailability.textContent = `⚠️ Doctor is unavailable today`;
                doctorAvailability.classList.remove('hidden', 'bg-blue-50', 'bg-yellow-50');
                doctorAvailability.classList.add('bg-yellow-50', 'text-yellow-700');
            }
            if (timeInput) {
                timeInput.value = '';
                timeInput.disabled = true;
            }
        } else if(date && start && end) {
            if (doctorAvailability) {
                doctorAvailability.textContent = `✓ Available on ${date} from ${start} to ${end}`;
                doctorAvailability.classList.remove('hidden', 'bg-yellow-50');
                doctorAvailability.classList.add('bg-blue-50', 'text-blue-700');
            }
            if (timeInput) {
                timeInput.min = start;
                timeInput.max = end;
                timeInput.disabled = false;
            }
        } else {
            if (doctorAvailability) doctorAvailability.classList.add('hidden');
            if (timeInput) {
                timeInput.value = '';
                timeInput.removeAttribute('min');
                timeInput.removeAttribute('max');
                timeInput.disabled = false;
            }
        }
    });

    if (doctorSelect.value) {
        doctorSelect.dispatchEvent(new Event('change'));
    }
}

function closeReasonModal(){
    const modal = document.getElementById('reasonModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

window.addEventListener('click', function(e) {
    const appointModal = document.getElementById('appointmentModal');
    const reasonMod = document.getElementById('reasonModal');
    
    if (e.target === appointModal && appointModal) {
        appointModal.classList.add('hidden');
        appointModal.classList.remove('flex');
    }
    if (e.target === reasonMod && reasonMod) {
        reasonMod.classList.add('hidden');
        reasonMod.classList.remove('flex');
    }
});
</script>
@endsection