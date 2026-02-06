@extends('layouts.app')

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
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4">
                            <p class="font-medium text-gray-800">{{ $appointment->patient_name }}</p>
                            <p class="text-xs text-gray-500">#APT-{{ $appointment->id }}</p>
                        </td>
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
                        <td class="text-center space-x-2">
                            @if($appointment->status == 'Pending')
                                <form action="{{ route('admin.appointments.approve', $appointment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800"><i class="fa fa-check"></i></button>
                                </form>
                                <form action="{{ route('admin.appointments.cancel', $appointment->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa fa-times"></i></button>
                                </form>
                            @endif
                            <a href="{{ route('admin.appointments.show', $appointment->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
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
@endsection
