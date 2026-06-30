<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\AppointmentRepository;
use ClinicManagement\Services\AppointmentAdminService;
use ClinicManagement\Validators\AppointmentValidator;
use ClinicManagement\Classes\AppointmentListTable;
use ClinicManagement\Repositories\DoctorRepository;
use ClinicManagement\Repositories\PatientRepository;

class AppointmentController
{
    protected $appointmentRepo;
    protected $appointmentService;
    protected $validator;
    protected $doctorRepo;
    protected $patientRepo;

    public function __construct(
        AppointmentRepository $appointmentRepo,
        AppointmentAdminService $appointmentService,
        AppointmentValidator $validator,
        DoctorRepository $doctorRepo,
        PatientRepository $patientRepo
    ) {
        $this->appointmentRepo = $appointmentRepo;
        $this->appointmentService = $appointmentService;
        $this->validator = $validator;
        $this->doctorRepo = $doctorRepo;
        $this->patientRepo = $patientRepo;
    }

    public function index()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_appointment_' . $id)) {
                $this->appointmentService->deleteAppointment($id);
                echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-appointments&deleted=1')) . "');</script>";
                exit;
            }
        }

        $listTable = new AppointmentListTable($this->appointmentRepo);
        $listTable->prepare_items();
        
        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/appointments/index.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $appointment = $this->appointmentRepo->find($id);

        if (!$appointment) {
            echo "Appointment not found.";
            return;
        }

        $doctor = $this->doctorRepo->find($appointment->doctor_id);
        $patient = $this->patientRepo->find($appointment->patient_id);

        $errors = [];
        $data = (array) $appointment;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_appointment'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit_appointment_nonce')) {
                
                // Handle status change
                if (isset($_POST['status']) && $_POST['status'] !== $appointment->status) {
                    $newStatus = sanitize_text_field($_POST['status']);
                    $this->appointmentService->updateStatus($id, $newStatus);
                    $appointment->status = $newStatus;
                    $data['status'] = $newStatus;
                }

                // Handle rescheduling
                if (isset($_POST['reschedule']) && $_POST['reschedule'] === '1') {
                    $postData = stripslashes_deep($_POST['appointment']);
                    $validation = $this->validator->validateReschedule($postData);

                    if ($validation === true) {
                        $result = $this->appointmentService->reschedule($id, $postData['appointment_date'], $postData['appointment_time']);
                        if ($result) {
                            echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-appointments&updated=1')) . "');</script>";
                            exit;
                        } else {
                            $errors[] = "Failed to reschedule appointment.";
                        }
                    } else {
                        $errors = $validation;
                        $data = array_merge($data, $postData);
                    }
                } else {
                    echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-appointments&updated=1')) . "');</script>";
                    exit;
                }

            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/appointments/edit.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }
}
