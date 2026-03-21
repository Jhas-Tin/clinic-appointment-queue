@extends('layouts.doctor')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">

    <!-- WELCOME SECTION -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, Dr. {{ Auth::guard('doctor')->user()->name }}</h2>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F d, Y') }}</p>
            </div>
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fa fa-user-md text-3xl"></i>
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
                    My Appointments
                </h3>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $appointments->count() ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total consultations</p>
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
                        <p class="text-3xl font-bold text-yellow-600">{{ $appointments->where('status','Pending')->count() ?? 0 }}</p>
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
                        <p class="text-3xl font-bold text-green-600">{{ $appointments->where('status','Approved')->count() ?? 0 }}</p>
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
                        <p class="text-3xl font-bold text-red-600">{{ $appointments->where('status','Cancelled')->count() ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Cancelled appointments</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fa fa-ban text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TODAY'S SCHEDULE CARD -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-calendar-day mr-2"></i>
                    Today's Schedule
                </h3>
                <span class="text-xs bg-white bg-opacity-20 text-white px-3 py-1 rounded-full">
                    {{ now()->format('l, F d, Y') }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            @php
                $todayAppointments = $appointments->where('date', now()->format('Y-m-d'));
                $ongoingAppointments = $todayAppointments->where('status', 'Approved')->where('time', '<=', now()->format('H:i:s'));
                $upcomingAppointments = $todayAppointments->where('status', 'Approved')->where('time', '>', now()->format('H:i:s'));
            @endphp

            @if($todayAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($todayAppointments as $appointment)
                        <div class="flex items-center justify-between p-4 rounded-xl {{ $appointment->status == 'Approved' && $appointment->time <= now()->format('H:i:s') ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-200' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-bold text-lg">{{ substr($appointment->patient_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $appointment->patient_name }}</p>
                                    <p class="text-xs text-gray-500">#APT-{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-sm font-mono font-semibold text-gray-700">{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</p>
                                    @if($appointment->status == 'Approved' && $appointment->time <= now()->format('H:i:s'))
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full flex items-center mt-1">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1 animate-pulse"></span>
                                            Ongoing
                                        </span>
                                    @endif
                                </div>
                                <span class="px-3 py-1.5 text-xs rounded-full font-semibold
                                    {{ $appointment->status == 'Approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $appointment->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $appointment->status == 'Cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $appointment->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <i class="fa fa-calendar-times text-5xl mb-3"></i>
                    <p class="text-lg font-medium">No appointments today</p>
                    <p class="text-sm">Your schedule is clear for today</p>
                </div>
            @endif
        </div>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fa fa-list mr-2"></i>
                    All Appointments
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cancel Reason</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
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
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center w-fit border {{ $color }}">
                                    <i class="fa {{ $icon }} mr-1.5"></i>
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($appointment->cancel_reason)
                                    <button type="button" class="view-reason-btn text-sm text-red-600 hover:text-red-800 bg-red-50 px-3 py-1 rounded-full flex items-center w-fit"
                                        data-reason="{{ $appointment->cancel_reason }}">
                                        <i class="fa fa-eye mr-1"></i>
                                        View Reason
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($appointment->status == 'Pending')
                                        <!-- APPROVE BUTTON (Opens Modal like Cancel) -->
                                        <button type="button" class="approve-btn w-8 h-8 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition flex items-center justify-center"
                                            data-id="{{ $appointment->id }}"
                                            data-patient="{{ $appointment->patient_name }}"
                                            title="Approve Appointment">
                                            <i class="fa fa-check"></i>
                                        </button>

                                        <!-- CANCEL BUTTON -->
                                        <button type="button" class="cancel-btn w-8 h-8 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition flex items-center justify-center"
                                            data-id="{{ $appointment->id }}"
                                            data-patient="{{ $appointment->patient_name }}"
                                            title="Cancel Appointment">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @elseif($appointment->status == 'Approved')
                                        <!-- SEND EMAIL BUTTON -->
                                        <button type="button" class="send-email-btn w-8 h-8 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition flex items-center justify-center"
                                            data-id="{{ $appointment->id }}"
                                            data-patient="{{ $appointment->patient_name }}"
                                            data-email="{{ $appointment->email }}"
                                            data-date="{{ $appointment->date }}"
                                            data-time="{{ $appointment->time }}"
                                            data-doctor="{{ $appointment->doctor_name }}"
                                            data-parent="{{ $appointment->parent_guardian }}"
                                            data-emergency="{{ $appointment->emergency_contact }}"
                                            title="Send Consultation Email">
                                            <i class="fa fa-envelope"></i>
                                        </button>
                                    @endif

                                    <!-- DELETE BUTTON -->
                                    <form action="{{ route('doctor.appointments.destroy', $appointment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 bg-gray-50 text-gray-600 rounded-lg hover:bg-gray-100 transition flex items-center justify-center" title="Delete Appointment">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="fa fa-calendar-times text-5xl mb-3"></i>
                                    <p class="text-lg font-medium">No appointments found</p>
                                    <p class="text-sm">Your appointment list is empty</p>
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

<!-- APPROVE MODAL -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-96 p-6 relative animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fa fa-check-circle text-green-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Approve Appointment</h3>
        </div>
        
        <p class="text-gray-700 mb-2">Are you sure you want to approve this appointment?</p>
        <p class="font-semibold text-gray-900 mb-4" id="approvePatientName"></p>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4">
            <div class="flex items-start gap-2">
                <i class="fa fa-info-circle text-yellow-600 mt-0.5"></i>
                <p class="text-xs text-yellow-800">
                    Approving this appointment will confirm the schedule. You can send the consultation summary with diagnosis and prescription after the consultation ends.
                </p>
            </div>
        </div>
        
        <form id="approveForm" method="POST">
            @csrf
            <div class="flex justify-end gap-3">
                <button type="button" id="closeApproveModal" class="px-4 py-2 border-2 border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition font-medium shadow-lg shadow-green-200 flex items-center gap-2">
                    <i class="fa fa-check-circle"></i>
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SEND EMAIL MODAL -->
<div id="sendEmailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative animate-fade-in max-h-[90vh] overflow-y-auto">
        <div class="flex items-center gap-3 mb-4 sticky top-0 bg-white pb-2 border-b">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fa fa-envelope text-blue-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Send Consultation Email</h3>
        </div>
        
        <!-- Patient Details -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                <i class="fa fa-user mr-1"></i>
                Patient Information
            </h4>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500">Patient</p>
                    <p class="font-medium text-gray-800" id="emailPatient"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="font-medium text-gray-800" id="emailAddress"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Date</p>
                    <p class="font-medium text-gray-800" id="emailDate"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Time</p>
                    <p class="font-medium text-gray-800" id="emailTime"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Doctor</p>
                    <p class="font-medium text-gray-800" id="emailDoctor"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Parent/Guardian</p>
                    <p class="font-medium text-gray-800" id="emailParent"></p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500">Emergency Contact</p>
                    <p class="font-medium text-gray-800" id="emailEmergency"></p>
                </div>
            </div>
        </div>

        <!-- Email Form -->
        <form id="sendEmailForm" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-stethoscope text-blue-500 mr-1"></i>
                    Diagnosis
                </label>
                <textarea name="diagnosis" id="diagnosisInput" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" placeholder="Enter diagnosis..." required></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-capsules text-purple-500 mr-1"></i>
                    Prescription (Medicine)
                </label>
                <select name="medicine_id" id="medicineId" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                    <option value="">-- Select Medicine --</option>
                    @foreach(\App\Models\Inventory::all() as $medicine)
                        <option value="{{ $medicine->id }}" data-stock="{{ $medicine->quantity }}">
                            {{ $medicine->name }} (Stock: {{ $medicine->quantity }} {{ $medicine->unit ?? 'pcs' }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="medicine_quantity" id="medicineQuantity" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" min="1" placeholder="Enter quantity">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fa fa-heartbeat text-red-500 mr-1"></i>
                    Patient Status
                </label>
                <select name="patient_status" id="patientStatus" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition" required>
                    <option value="">-- Select Status --</option>
                    <option value="Go Home">Go Home</option>
                    <option value="Stay">Stay</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" id="closeEmailModal" class="px-4 py-2 border-2 border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-medium shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fa fa-paper-plane"></i>
                    Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CANCEL MODAL -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-96 p-6 relative animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fa fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Cancel Appointment</h3>
        </div>
        
        <p id="modalPatient" class="text-gray-700 mb-4"></p>
        
        <form id="cancelForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation</label>
                <textarea name="cancel_reason" id="reasonInput" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition" placeholder="Enter reason..." required></textarea>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="closeModal" class="px-4 py-2 border-2 border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 transition font-medium shadow-lg shadow-red-200 flex items-center gap-2">
                    <i class="fa fa-times-circle"></i>
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CANCEL REASON MODAL -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-96 p-6 relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <i class="fa fa-info-circle text-red-500 mr-2"></i>
                Cancellation Reason
            </h3>
            <button id="closeReasonModal" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p id="reasonText" class="text-gray-800"></p>
        </div>
        
        <div class="flex justify-end mt-4">
            <button type="button" id="closeReasonModalBtn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">Close</button>
        </div>
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // APPROVE MODAL
    const approveModal = document.getElementById('approveModal');
    const approveForm = document.getElementById('approveForm');
    const approvePatientName = document.getElementById('approvePatientName');
    const closeApproveModal = document.getElementById('closeApproveModal');

    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            approvePatientName.textContent = this.dataset.patient;
            approveForm.action = "{{ url('doctor/appointments') }}/" + id + "/approve";
            approveModal.classList.remove('hidden');
            approveModal.classList.add('flex');
        });
    });

    closeApproveModal.addEventListener('click', function() {
        approveModal.classList.add('hidden');
        approveModal.classList.remove('flex');
    });

    // SEND EMAIL MODAL
    const sendEmailModal = document.getElementById('sendEmailModal');
    const sendEmailForm = document.getElementById('sendEmailForm');
    const emailPatient = document.getElementById('emailPatient');
    const emailAddress = document.getElementById('emailAddress');
    const emailDate = document.getElementById('emailDate');
    const emailTime = document.getElementById('emailTime');
    const emailDoctor = document.getElementById('emailDoctor');
    const emailParent = document.getElementById('emailParent');
    const emailEmergency = document.getElementById('emailEmergency');
    const closeEmailModal = document.getElementById('closeEmailModal');

    document.querySelectorAll('.send-email-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            emailPatient.textContent = this.dataset.patient;
            emailAddress.textContent = this.dataset.email;
            emailDate.textContent = new Date(this.dataset.date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            emailTime.textContent = new Date('1970-01-01T' + this.dataset.time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            emailDoctor.textContent = this.dataset.doctor;
            emailParent.textContent = this.dataset.parent || 'N/A';
            emailEmergency.textContent = this.dataset.emergency || 'N/A';
            sendEmailForm.action = "{{ url('doctor/appointments') }}/" + id + "/send-email";
            sendEmailModal.classList.remove('hidden');
            sendEmailModal.classList.add('flex');

            document.getElementById('diagnosisInput').value = '';
            document.getElementById('medicineId').value = '';
            document.getElementById('medicineQuantity').value = '';
            document.getElementById('patientStatus').value = '';
        });
    });

    closeEmailModal.addEventListener('click', function() {
        sendEmailModal.classList.add('hidden');
        sendEmailModal.classList.remove('flex');
    });

    // CANCEL MODAL
    const cancelModal = document.getElementById('cancelModal');
    const modalPatient = document.getElementById('modalPatient');
    const reasonInput = document.getElementById('reasonInput');
    const cancelForm = document.getElementById('cancelForm');
    const closeModal = document.getElementById('closeModal');

    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            modalPatient.textContent = `Patient: ${this.dataset.patient}`;
            reasonInput.value = '';
            cancelForm.action = "{{ url('doctor/appointments') }}/" + this.dataset.id + "/cancel";
            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');
        });
    });

    closeModal.addEventListener('click', function() {
        cancelModal.classList.add('hidden');
        cancelModal.classList.remove('flex');
    });

    // CANCEL REASON MODAL
    const reasonModal = document.getElementById('reasonModal');
    const reasonText = document.getElementById('reasonText');
    const closeReasonModal = document.getElementById('closeReasonModal');
    const closeReasonModalBtn = document.getElementById('closeReasonModalBtn');

    document.querySelectorAll('.view-reason-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            reasonText.textContent = this.dataset.reason;
            reasonModal.classList.remove('hidden');
            reasonModal.classList.add('flex');
        });
    });

    closeReasonModal.addEventListener('click', function() {
        reasonModal.classList.add('hidden');
        reasonModal.classList.remove('flex');
    });
    
    closeReasonModalBtn.addEventListener('click', function() {
        reasonModal.classList.add('hidden');
        reasonModal.classList.remove('flex');
    });

    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === approveModal) {
            approveModal.classList.add('hidden');
            approveModal.classList.remove('flex');
        }
        if (e.target === sendEmailModal) {
            sendEmailModal.classList.add('hidden');
            sendEmailModal.classList.remove('flex');
        }
        if (e.target === cancelModal) {
            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');
        }
        if (e.target === reasonModal) {
            reasonModal.classList.add('hidden');
            reasonModal.classList.remove('flex');
        }
    });
});
</script>
@endsection