<h2>Appointment Update</h2>

<p>Hello {{ $appointment->parent_guardian ?? $appointment->patient_name }},</p>

<p>Your appointment with Dr. {{ $appointment->doctor_name }} on 
   {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }} at 
   {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }} has been reviewed.</p>

@if($appointment->diagnosis)
    <p><strong>Diagnosis:</strong> {{ $appointment->diagnosis }}</p>
@endif

@if($appointment->prescription)
    <p><strong>Prescribed Medicine:</strong> {{ $appointment->prescription }}</p>
@endif

@if($appointment->patient_status)
    <p>
        @if($appointment->patient_status === 'Go Home')
            The patient should rest at home.    
        @elseif($appointment->patient_status === 'Stay')
            The patient should stay in the clinic for further observation.
        @else
            {{ $appointment->patient_status }}
        @endif
    </p>
@endif

<p>Thank you!</p>