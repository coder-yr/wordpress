<?php

namespace ClinicManagement\Controllers;

class FrontendController
{
    public function registerShortcodes()
    {
        add_shortcode('clinic_patient_dashboard', [$this, 'renderPatientDashboard']);
        add_shortcode('clinic_doctor_dashboard', [$this, 'renderDoctorDashboard']);
        add_shortcode('clinic_admin_dashboard', [$this, 'renderAdminDashboard']);
    }

    public function renderPatientDashboard()
    {
        if (!is_user_logged_in() || !current_user_can('clinic_patient')) {
            return '<div>You must be logged in as a patient to view this dashboard. <a href="'.wp_login_url(get_permalink()).'">Login Here</a></div>';
        }

        // Fetch patient ID
        $patientRepo = \ClinicManagement\Bootstrap\Application::getInstance()->make(\ClinicManagement\Repositories\PatientRepository::class);
        $patient = $patientRepo->findByUserId(get_current_user_id());
        $appointments = [];
        
        if ($patient) {
            $appointmentRepo = \ClinicManagement\Bootstrap\Application::getInstance()->make(\ClinicManagement\Repositories\AppointmentRepository::class);
            $appointments = $appointmentRepo->getPatientAppointments($patient->id);
        }

        ob_start();
        include CLINIC_PLUGIN_DIR . 'views/patient/dashboard.php';
        return ob_get_clean();
    }

    public function renderDoctorDashboard()
    {
        if (!is_user_logged_in() || !current_user_can('clinic_doctor')) {
            return '<div>You must be logged in as a doctor to view this dashboard. <a href="'.wp_login_url(get_permalink()).'">Login Here</a></div>';
        }

        ob_start();
        include CLINIC_PLUGIN_DIR . 'views/doctor/dashboard.php';
        return ob_get_clean();
    }

    public function renderAdminDashboard()
    {
        if (!is_user_logged_in() || (!current_user_can('clinic_administrator') && !current_user_can('clinic_receptionist'))) {
            return '<div>You do not have permission to view the admin dashboard.</div>';
        }

        ob_start();
        include CLINIC_PLUGIN_DIR . 'views/admin/dashboard.php';
        return ob_get_clean();
    }
}
