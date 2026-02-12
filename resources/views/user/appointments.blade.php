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

    <!-- BOOK APPOINTMENT BUTTON -->
    <div class="flex justify-end mb-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition"
            onclick="document.getElementById('appointmentModal').classList.remove('hidden')">
            Book Appointment
        </button>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-4">Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <!-- <th class="text-center">Actions</th> -->
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($appointments as $appointment)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">{{ $appointment->patient_name }}<br><span class="text-xs text-gray-500">#APT-{{ $appointment->id }}</span></td>
                    <td>{{ $appointment->doctor_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
                    <td>
                        <span class="px-3 py-1 text-xs rounded-full
                            {{ $appointment->status == 'Approved' ? 'bg-green-100 text-green-600' : '' }}
                            {{ $appointment->status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                            {{ $appointment->status == 'Cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                            {{ $appointment->status }}
                        </span>
                    </td>
                    <!-- <td class="text-center">
                        <span class="text-gray-500">—</span>
                    </td> -->
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-6 text-gray-500">No appointments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- BOOKING MODAL -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md relative">
        <button onclick="document.getElementById('appointmentModal').classList.add('hidden')"
            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Book an Appointment</h2>

        <form action="{{ route('user.appointments.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="text" name="doctor_name" placeholder="Doctor Name" class="w-full border p-2 rounded-xl" required>
            <input type="date" name="date" class="w-full border p-2 rounded-xl" required>
            <input type="time" name="time" class="w-full border p-2 rounded-xl" required>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">Book</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('keydown', function(e){
        if(e.key === "Escape"){
            document.getElementById('appointmentModal').classList.add('hidden');
        }
    });
</script>
@endsection
