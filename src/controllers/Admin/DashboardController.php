<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\DoctorRepository;
use ClinicManagement\Repositories\PatientRepository;
use ClinicManagement\Repositories\AppointmentRepository;

class DashboardController
{
    protected $doctorRepo;
    protected $patientRepo;
    protected $appointmentRepo;

    public function __construct(
        DoctorRepository $doctorRepo,
        PatientRepository $patientRepo,
        AppointmentRepository $appointmentRepo
    ) {
        $this->doctorRepo = $doctorRepo;
        $this->patientRepo = $patientRepo;
        $this->appointmentRepo = $appointmentRepo;
    }

    public function index()
    {
        $stats = [
            'total_doctors' => $this->doctorRepo->count(),
            'total_patients' => $this->patientRepo->count(),
            'todays_appointments' => $this->appointmentRepo->countTodays(),
            'upcoming_appointments' => $this->appointmentRepo->countUpcoming(),
        ];

        $recentPatients = $this->patientRepo->getRecent(5);
        $recentAppointments = $this->appointmentRepo->getRecent(5);

        // Load the view
        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/dashboard.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Dashboard view not found.";
        }
    }
}
