@extends('layouts.doctor')

@section('title', 'Availability')

@section('content')

<div class="bg-white p-6 rounded-2xl shadow w-96">

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.availability.update') }}">
        @csrf

        <label class="block mb-1 font-medium">Available Date</label>
        <input type="date"
               name="available_date"
               value="{{ $doctor->available_date?->format('Y-m-d') }}"
               class="border p-2 w-full rounded mb-3"
               required>

        <label class="block mb-1 font-medium">Start Time</label>
        <input type="time"
               name="start_time"
               value="{{ $doctor->start_time }}"
               class="border p-2 w-full rounded mb-3"
               required>

        <label class="block mb-1 font-medium">End Time</label>
        <input type="time"
               name="end_time"
               value="{{ $doctor->end_time }}"
               class="border p-2 w-full rounded mb-3"
               required>

        <label class="block mb-1 font-medium">Status</label>
        <select name="availability_status" class="border p-2 w-full rounded mb-3" required>
            <option value="Available" {{ $doctor->availability_status=='Available'?'selected':'' }}>Available</option>
            <option value="Unavailable" {{ $doctor->availability_status=='Unavailable'?'selected':'' }}>Unavailable</option>
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Availability</button>
    </form>

    @if($doctor->start_time)
        <hr class="my-4">

        <h3 class="font-semibold mb-2">Current Schedule</h3>
        <p>
            <strong>Date:</strong> {{ $doctor->available_date?->format('M d, Y') }} <br>
            <strong>Time:</strong> 
            {{ \Carbon\Carbon::parse($doctor->start_time)->format('h:i A') }} -
            {{ \Carbon\Carbon::parse($doctor->end_time)->format('h:i A') }} <br>
            <strong>Status:</strong> {{ $doctor->availability_status }}
        </p>
    @endif

</div>

@endsection