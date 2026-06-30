<?php

namespace ClinicManagement\Listeners;

use ClinicManagement\Events\AppointmentBooked;

class LogActivity
{
    public function handle(AppointmentBooked $event)
    {
        // Placeholder for inserting into `clinic_activity_logs` table
        error_log("Activity Logged: Appointment #{$event->appointmentId} booked successfully.");
    }
}
