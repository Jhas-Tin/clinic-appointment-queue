<h2>Appointment Approved</h2>

<p>Hello {{ $appointment->patient_name }},</p>

<p>Your appointment has been approved.</p>

<p>Doctor: {{ $appointment->doctor_name }}</p>
<p>Date: {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</p>
<p>Time: {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</p>

<p>Status: Approved</p>