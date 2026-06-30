<?php

namespace ClinicManagement\Listeners;

use ClinicManagement\Events\AppointmentBooked;

class SendBookingEmail
{
    public function handle(AppointmentBooked $event)
    {
        // Fetch patient email using $event->data['patient_id'] or $event->appointmentId
        // This is a placeholder for the actual email sending logic via wp_mail()
        
        $patientId = $event->data['patient_id'] ?? 0;
        $patientUser = get_userdata($patientId);

        if ($patientUser) {
            $to = $patientUser->user_email;
            $subject = 'Appointment Confirmed - ' . ($event->data['token'] ?? '');
            
            $message = "Hello {$patientUser->display_name},\n\n";
            $message .= "Your appointment is confirmed for {$event->data['appointment_date']} at {$event->data['start_time']}.\n";
            $message .= "Your Token Number is: " . ($event->data['token'] ?? '') . "\n\n";
            
            if (isset($event->data['meeting_link'])) {
                $message .= "Your consultation link is securely generated and will be available in your dashboard 10 minutes prior to the appointment.\n\n";
            }
            
            $message .= "Thank you,\nClinic Management Team";

            // Uncomment in production
            // wp_mail($to, $subject, $message);
            error_log("Sending email to $to: $subject");
        }
    }
}
