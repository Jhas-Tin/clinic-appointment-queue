@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- STATS CARD FOR THIS APPOINTMENT -->
    <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h1 class="text-2xl font-bold mb-4">Appointment Details</h1>
        <div class="grid grid-cols-2 gap-4 text-gray-700">
            <p><span class="font-semibold">ID:</span> #APT-{{ $appointment->id }}</p>
            <p><span class="font-semibold">Patient Name:</span> {{ $appointment->patient_name }}</p>
            <p><span class="font-semibold">Doctor Name:</span> {{ $appointment->doctor_name }}</p>
            <!-- <p><span class="font-semibold">Email:</span> {{ $appointment->email }}</p> -->
            <p><span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</p>
            <p><span class="font-semibold">Time:</span> {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</p>
            <p><span class="font-semibold">Status:</span> 
                <span class="px-3 py-1 text-xs rounded-full
                    {{ $appointment->status == 'Approved' ? 'bg-green-100 text-green-600' : '' }}
                    {{ $appointment->status == 'Pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                    {{ $appointment->status == 'Cancelled' ? 'bg-red-100 text-red-600' : '' }}">
                    {{ $appointment->status }}
                </span>
            </p>
        </div>
    </div>

    <div class="flex space-x-3">
        @if($appointment->status == 'Pending')
            <form action="{{ route('admin.appointments.approve', $appointment->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Approve
                </button>
            </form>
            <form action="{{ route('admin.appointments.cancel', $appointment->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Cancel
                </button>
            </form>
        @endif

        <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
                Delete
            </button>
        </form>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.appointments') }}" class="text-blue-500 underline">Back to Appointments</a>
    </div>

</div>
@endsection