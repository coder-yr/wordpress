<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\PatientRepository;
use ClinicManagement\Services\PatientService;
use ClinicManagement\Validators\PatientValidator;
use ClinicManagement\Classes\PatientListTable;

class PatientController
{
    protected $patientRepo;
    protected $patientService;
    protected $validator;

    public function __construct(
        PatientRepository $patientRepo,
        PatientService $patientService,
        PatientValidator $validator
    ) {
        $this->patientRepo = $patientRepo;
        $this->patientService = $patientService;
        $this->validator = $validator;
    }

    public function index()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_patient_' . $id)) {
                $this->patientService->deletePatient($id);
                echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-patients&deleted=1')) . "');</script>";
                exit;
            }
        }

        $listTable = new PatientListTable($this->patientRepo);
        $listTable->prepare_items();
        
        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/patients/index.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function create()
    {
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_patient'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'create_patient_nonce')) {
                $data = stripslashes_deep($_POST['patient']);
                $validation = $this->validator->validate($data);

                if ($validation === true) {
                    $result = $this->patientService->createPatient($data);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-patients&created=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to create patient.";
                    }
                } else {
                    $errors = $validation;
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/patients/create.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $patient = $this->patientRepo->find($id);

        if (!$patient) {
            echo "Patient not found.";
            return;
        }

        $errors = [];
        $data = (array) $patient;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_patient'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit_patient_nonce')) {
                $postData = stripslashes_deep($_POST['patient']);
                $validation = $this->validator->validate($postData);

                if ($validation === true) {
                    $result = $this->patientService->updatePatient($id, $postData);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-patients&updated=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to update patient.";
                    }
                } else {
                    $errors = $validation;
                    $data = array_merge($data, $postData);
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/patients/edit.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }
}
