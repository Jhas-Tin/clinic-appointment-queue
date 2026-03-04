<h2>Appointment Cancelled</h2>

<p>Hello {{ $appointment->patient_name }},</p>

<p>Your appointment has been cancelled.</p>

<p><b>Doctor:</b> {{ $appointment->doctor_name }}</p>
<p><b>Date:</b> {{ $appointment->date }}</p>
<p><b>Time:</b> {{ $appointment->time }}</p>

<p><b>Reason:</b></p>

<p style="color:red">
    {{ $appointment->cancel_reason }}
</p>

<p>Please reschedule if needed.</p>

<p>Clinic Appointment System</p>