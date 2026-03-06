@extends('layouts.doctor')

@section('title', 'Availability')

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- STATS CARDS (Optional, you can remove if not needed) -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Availability Status</p>
            <h2 class="text-2xl font-bold text-blue-600">{{ $doctor->availability_status ?? 'Not Set' }}</h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Start Time</p>
            <h2 class="text-2xl font-bold text-gray-700">
                {{ $doctor->start_time ? \Carbon\Carbon::parse($doctor->start_time)->format('h:i A') : '-' }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">End Time</p>
            <h2 class="text-2xl font-bold text-gray-700">
                {{ $doctor->end_time ? \Carbon\Carbon::parse($doctor->end_time)->format('h:i A') : '-' }}
            </h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Available Date</p>
            <h2 class="text-2xl font-bold text-gray-700">{{ $doctor->available_date?->format('M d, Y') ?? '-' }}</h2>
        </div>
    </div>

    <!-- AVAILABILITY FORM -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-xl font-semibold mb-4">Update Availability</h3>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('doctor.availability.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Available Date</label>
                    <input type="date"
                           name="available_date"
                           value="{{ $doctor->available_date?->format('Y-m-d') }}"
                           class="border p-2 w-full rounded"
                           required>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Start Time</label>
                    <input type="time"
                           name="start_time"
                           value="{{ $doctor->start_time }}"
                           class="border p-2 w-full rounded"
                           required>
                </div>

                <div>
                    <label class="block mb-1 font-medium">End Time</label>
                    <input type="time"
                           name="end_time"
                           value="{{ $doctor->end_time }}"
                           class="border p-2 w-full rounded"
                           required>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Status</label>
                    <select name="availability_status" class="border p-2 w-full rounded" required>
                        <option value="Available" {{ $doctor->availability_status=='Available'?'selected':'' }}>Available</option>
                        <option value="Unavailable" {{ $doctor->availability_status=='Unavailable'?'selected':'' }}>Unavailable</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </div>

    {{-- <!-- CURRENT SCHEDULE -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-xl font-semibold mb-4">Current Schedule</h3>
        @if($doctor->start_time)
            <p><strong>Date:</strong> {{ $doctor->available_date?->format('M d, Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($doctor->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($doctor->end_time)->format('h:i A') }}</p>
            <p><strong>Status:</strong> {{ $doctor->availability_status }}</p>
        @else
            <p class="text-gray-500">No schedule set yet.</p>
        @endif
    </div> --}}

</div>
@endsection