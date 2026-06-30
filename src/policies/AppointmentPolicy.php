<?php

namespace ClinicManagement\Policies;

use ClinicManagement\Bootstrap\Application;

class AppointmentPolicy
{
    /**
     * Determine if the user can book an appointment.
     *
     * @param \WP_User $user
     * @return bool
     */
    public static function canBook(\WP_User $user): bool
    {
        return $user->has_cap('book_clinic_appointments');
    }

    /**
     * Determine if the user can view a specific appointment.
     *
     * @param \WP_User $user
     * @param int $appointmentId
     * @return bool
     */
    public static function canView(\WP_User $user, int $appointmentId): bool
    {
        if ($user->has_cap('manage_clinic_options')) {
            return true; // Admin can view all
        }

        $app = Application::getInstance();
        $repo = $app->make(\ClinicManagement\Repositories\AppointmentRepository::class);
        $appointment = $repo->find($appointmentId);

        if (!$appointment) {
            return false;
        }

        if ($user->has_cap('clinic_patient')) {
            // Must be the patient who booked it
            $patientRepo = $app->make(\ClinicManagement\Repositories\PatientRepository::class);
            $patient = $patientRepo->findByUserId($user->ID);
            return $patient && $patient->id == $appointment->patient_id;
        }

        if ($user->has_cap('clinic_doctor')) {
            // Must be the assigned doctor
            $doctorRepo = $app->make(\ClinicManagement\Repositories\DoctorRepository::class);
            $doctor = $doctorRepo->findByUserId($user->ID);
            return $doctor && $doctor->id == $appointment->doctor_id;
        }

        if ($user->has_cap('clinic_receptionist')) {
            return true;
        }

        return false;
    }
}
