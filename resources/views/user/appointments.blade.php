@extends('layouts.user')

@section('title', 'Appointments')

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- STATS CARDS -->
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Total Appointments</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $appointments->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Pending</p>
            <h2 class="text-2xl font-bold text-yellow-500">{{ $appointments->where('status','Pending')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Approved</p>
            <h2 class="text-2xl font-bold text-green-600">{{ $appointments->where('status','Approved')->count() }}</h2>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-gray-500">Cancelled</p>
            <h2 class="text-2xl font-bold text-red-500">{{ $appointments->where('status','Cancelled')->count() }}</h2>
        </div>
    </div>

    <!-- BOOK BUTTON -->
    <div class="flex justify-end mb-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700"
            onclick="document.getElementById('appointmentModal').classList.remove('hidden')">
            Book Appointment
        </button>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-4">Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($appointments as $appointment)
                <tr>
                    <td class="p-4">
                        {{ $appointment->patient_name }}
                        <br>
                        <span class="text-xs text-gray-500">#APT-{{ $appointment->id }}</span>
                    </td>
                    <td>{{ $appointment->doctor_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
                    <td>
                        @if($appointment->status == 'Approved')
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-600">Approved</span>
                        @endif
                        @if($appointment->status == 'Pending')
                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">Pending</span>
                        @endif
                        @if($appointment->status == 'Cancelled')
                            <button class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-600 showReasonBtn"
                                data-reason="{{ $appointment->cancel_reason }}">Cancelled</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-6 text-gray-500">No appointments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- CANCEL REASON MODAL -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl p-6 w-96">
        <h2 class="text-lg font-bold mb-3">Cancellation Reason</h2>
        <p id="reasonText" class="text-gray-700 mb-4"></p>
        <div class="text-right">
            <button onclick="closeReasonModal()" class="px-4 py-2 bg-gray-300 rounded-xl">Close</button>
        </div>
    </div>
</div>

<!-- BOOKING MODAL -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md relative">
        <button onclick="document.getElementById('appointmentModal').classList.add('hidden')"
            class="absolute top-4 right-4 text-gray-500">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Book an Appointment</h2>

        <form id="appointmentForm" action="{{ route('user.appointments.store') }}" method="POST" class="space-y-4">
            @csrf

            <input type="text" name="patient_name" placeholder="Full Name" class="w-full border p-2 rounded-xl" required>
            <input type="email" id="emailInput" name="email" placeholder="Email Address" class="w-full border p-2 rounded-xl" required>
            <input type="text" name="emergency_contact" placeholder="Emergency Contact" class="w-full border p-2 rounded-xl" required>
            <input type="text" name="parent_guardian" placeholder="Parent/Guardian" class="w-full border p-2 rounded-xl">

            <select id="doctorSelect" name="doctor_name" class="w-full border p-2 rounded-xl" required>
    <option value="">Select Doctor</option>
    @foreach($doctors as $doctor)
        @php
            $isUnavailable = $doctor->availability_status !== 'Available';
            $label = $doctor->name;
            if ($isUnavailable) {
                $label .= ' (Unavailable)';
            } elseif ($doctor->available_date) {
                $label .= ' (Available on ' . $doctor->available_date->format('Y-m-d') . ')';
            }
        @endphp
        <option value="{{ $doctor->name }}"
            data-date="{{ $doctor->available_date?->format('Y-m-d') }}"
            data-start="{{ $doctor->start_time }}"
            data-end="{{ $doctor->end_time }}"
            data-status="{{ $doctor->availability_status }}">
            {{ $label }}
        </option>
    @endforeach
</select>
            <p id="doctorAvailability" class="text-sm text-gray-600 mt-1"></p>

            <input type="date" id="dateInput" name="date" class="w-full border p-2 rounded-xl" required>
            <input type="time" id="timeInput" name="time" class="w-full border p-2 rounded-xl" required>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl">Book</button>
            </div>
        </form>
    </div>
</div>

<script>
// Show cancellation reason modal
document.querySelectorAll('.showReasonBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        const reason = this.dataset.reason || "No reason provided";
        document.getElementById('reasonText').innerText = reason;
        const modal = document.getElementById('reasonModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });
});

// Doctor availability
const doctorSelect = document.getElementById('doctorSelect');
const doctorAvailability = document.getElementById('doctorAvailability');
const dateInput = document.getElementById('dateInput');
const timeInput = document.getElementById('timeInput');

doctorSelect.addEventListener('change', function() {
    const selected = doctorSelect.selectedOptions[0];
    const date = selected.dataset.date;
    let start = selected.dataset.start;
    let end = selected.dataset.end;
    const status = selected.dataset.status; // now reading availability_status

    if (start && start.length > 5) start = start.substring(0,5);
    if (end && end.length > 5) end = end.substring(0,5);

    if(status !== 'Available') {
        doctorAvailability.textContent = `Doctor is unavailable`;
        dateInput.value = '';
        timeInput.value = '';
        dateInput.disabled = true;
        timeInput.disabled = true;
    } else if(date && start && end) {
        doctorAvailability.textContent = `Available on ${date} from ${start} to ${end}`;
        dateInput.value = date;
        dateInput.min = date;
        dateInput.max = date;
        dateInput.disabled = false;
        timeInput.value = '';
        timeInput.min = start;
        timeInput.max = end;
        timeInput.disabled = false;
    } else {
        doctorAvailability.textContent = '';
        dateInput.value = '';
        timeInput.value = '';
        dateInput.removeAttribute('min');
        dateInput.removeAttribute('max');
        timeInput.removeAttribute('min');
        timeInput.removeAttribute('max');
        dateInput.disabled = false;
        timeInput.disabled = false;
    }
});

// Simple alert notifications
window.onload = function() {
    @if ($errors->any())
        alert("{{ $errors->first() }}");
    @endif

    @if (session('success'))
        alert("{{ session('success') }}");
    @endif
};

function closeReasonModal(){
    const modal = document.getElementById('reasonModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection