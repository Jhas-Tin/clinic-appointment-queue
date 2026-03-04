@extends('layouts.doctor')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- STATS CARDS -->
    <div class="grid grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">My Appointments</p>
            <h2 class="text-2xl font-bold">{{ $appointments->count() ?? 0 }}</h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Pending</p>
            <h2 class="text-2xl font-bold text-yellow-500">{{ $appointments->where('status','Pending')->count() ?? 0 }}</h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Approved</p>
            <h2 class="text-2xl font-bold text-green-600">{{ $appointments->where('status','Approved')->count() ?? 0 }}</h2>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-500">Cancelled</p>
            <h2 class="text-2xl font-bold text-red-500">{{ $appointments->where('status','Cancelled')->count() ?? 0 }}</h2>
        </div>
    </div>

    <!-- APPOINTMENTS TABLE -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mt-6">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-4">Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Cancel Reason</th>
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
                        <td>
                            @if($appointment->cancel_reason)
                                <button type="button" class="text-sm text-red-600 underline view-reason-btn"
                                    data-reason="{{ $appointment->cancel_reason }}">
                                    View
                                </button>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center space-x-2">
                            @if($appointment->status == 'Pending')
                                <!-- APPROVE BUTTON (opens modal now) -->
                                <button type="button" class="text-green-600 hover:text-green-800 approve-btn"
                                    data-id="{{ $appointment->id }}"
                                    data-patient="{{ $appointment->patient_name }}"
                                    data-email="{{ $appointment->email }}"
                                    data-date="{{ $appointment->date }}"
                                    data-time="{{ $appointment->time }}"
                                    data-doctor="{{ $appointment->doctor_name }}"
                                    data-parent="{{ $appointment->parent_guardian }}"
                                    data-emergency="{{ $appointment->emergency_contact }}">
                                    <i class="fa fa-check"></i>
                                </button>

                                <!-- CANCEL BUTTON -->
                                <button type="button" class="text-red-500 hover:text-red-700 cancel-btn"
                                    data-id="{{ $appointment->id }}"
                                    data-patient="{{ $appointment->patient_name }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif

                            <!-- DELETE BUTTON -->
                            <form action="{{ route('doctor.appointments.destroy', $appointment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-500 hover:text-gray-800">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
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

<!-- CANCEL MODAL -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Cancel Appointment</h3>
        <p id="modalPatient" class="mb-4"></p>
        <form id="cancelForm" method="POST">
            @csrf
            <textarea name="cancel_reason" id="reasonInput" class="w-full border rounded p-2" placeholder="Enter reason" required></textarea>
            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" id="closeModal" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-500 text-white hover:bg-red-600">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- CANCEL REASON MODAL -->
<div id="reasonModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-96">
        <h3 class="text-lg font-bold mb-2">Cancel Reason</h3>
        <p id="reasonText" class="text-gray-800"></p>
        <div class="flex justify-end mt-4">
            <button type="button" id="closeReasonModal" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Close</button>
        </div>
    </div>
</div>

<!-- APPROVE MODAL -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-lg w-96 p-6">
        <h2 class="text-xl font-semibold mb-4">Approve Appointment</h2>
        <div class="space-y-1 text-sm">
            <p><b>Patient:</b> <span id="approvePatient"></span></p>
            <p><b>Email:</b> <span id="approveEmail"></span></p>
            <p><b>Date:</b> <span id="approveDate"></span></p>
            <p><b>Time:</b> <span id="approveTime"></span></p>
            <p><b>Doctor:</b> <span id="approveDoctor"></span></p>
            <p><b>Parent / Guardian:</b> <span id="approveParent"></span></p>
            <p><b>Emergency Contact:</b> <span id="approveEmergency"></span></p>
        </div>
        <form id="approveForm" method="POST" class="mt-4">
            @csrf
            <div class="flex justify-end gap-2">
                <button type="button" id="closeApproveModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Approve & Send Email</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================= APPROVE MODAL =================
    const approveModal = document.getElementById('approveModal');
    const approveForm = document.getElementById('approveForm');
    const approvePatient = document.getElementById('approvePatient');
    const approveEmail = document.getElementById('approveEmail');
    const approveDate = document.getElementById('approveDate');
    const approveTime = document.getElementById('approveTime');
    const approveDoctor = document.getElementById('approveDoctor');
    const approveParent = document.getElementById('approveParent');
    const approveEmergency = document.getElementById('approveEmergency');
    const closeApproveModal = document.getElementById('closeApproveModal');

    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            approvePatient.textContent = this.dataset.patient;
            approveEmail.textContent = this.dataset.email;
            approveDate.textContent = new Date(this.dataset.date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            approveTime.textContent = new Date('1970-01-01T' + this.dataset.time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            approveDoctor.textContent = this.dataset.doctor;
            approveParent.textContent = this.dataset.parent;
            approveEmergency.textContent = this.dataset.emergency;
            approveForm.action = "{{ url('doctor/appointments') }}/" + id + "/approve";
            approveModal.classList.remove('hidden');
            approveModal.classList.add('flex');
        });
    });

    closeApproveModal.addEventListener('click', function(){
        approveModal.classList.add('hidden');
        approveModal.classList.remove('flex');
    });

    // ================= CANCEL MODAL =================
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

    // ================= CANCEL REASON MODAL =================
    const reasonModal = document.getElementById('reasonModal');
    const reasonText = document.getElementById('reasonText');
    const closeReasonModal = document.getElementById('closeReasonModal');

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

});
</script>
@endsection