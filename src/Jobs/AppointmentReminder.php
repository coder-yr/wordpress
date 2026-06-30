<?php

namespace ClinicManagement\Jobs;

class AppointmentReminder
{
    /**
     * Register the cron hook if it doesn't exist.
     */
    public static function schedule()
    {
        if (!wp_next_scheduled('clinic_appointment_reminders')) {
            wp_schedule_event(time(), 'hourly', 'clinic_appointment_reminders');
        }
    }

    /**
     * Handle the scheduled task.
     */
    public static function handle()
    {
        global $wpdb;
        $appointmentsTable = $wpdb->prefix . 'clinic_appointments';

        // Find appointments for tomorrow that haven't been reminded yet
        // In a real app, we'd add a 'reminded' flag to the table.
        // For now, this is the query structure.
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $sql = $wpdb->prepare(
            "SELECT * FROM {$appointmentsTable} WHERE appointment_date = %s AND status = 'confirmed'", 
            $tomorrow
        );

        $appointments = $wpdb->get_results($sql);

        foreach ($appointments as $appointment) {
            self::sendReminder($appointment);
        }
    }

    protected static function sendReminder($appointment)
    {
        $patient = get_userdata($appointment->patient_id);
        if ($patient) {
            $to = $patient->user_email;
            $subject = 'Reminder: Your Clinic Appointment Tomorrow';
            $message = "Hi {$patient->display_name},\n\nJust a reminder that you have an appointment tomorrow at {$appointment->start_time}.";
            
            // wp_mail($to, $subject, $message);
            error_log("Sending reminder email to $to");
        }
    }
}
