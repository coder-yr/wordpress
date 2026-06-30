<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\DoctorRepository;
use ClinicManagement\Services\DoctorService;
use ClinicManagement\Validators\DoctorValidator;
use ClinicManagement\Classes\DoctorListTable;

class DoctorController
{
    protected $doctorRepo;
    protected $doctorService;
    protected $validator;

    public function __construct(
        DoctorRepository $doctorRepo,
        DoctorService $doctorService,
        DoctorValidator $validator
    ) {
        $this->doctorRepo = $doctorRepo;
        $this->doctorService = $doctorService;
        $this->validator = $validator;
    }

    public function index()
    {
        // Handle deletion if triggered
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_doctor_' . $id)) {
                $this->doctorService->deleteDoctor($id);
                // Redirect to avoid resubmission
                echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-doctors&deleted=1')) . "');</script>";
                exit;
            }
        }

        $listTable = new DoctorListTable($this->doctorRepo);
        $listTable->prepare_items();
        
        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/doctors/index.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function create()
    {
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_doctor'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'create_doctor_nonce')) {
                $data = stripslashes_deep($_POST['doctor']);
                $validation = $this->validator->validate($data);

                if ($validation === true) {
                    $result = $this->doctorService->createDoctor($data);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-doctors&created=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to create doctor (Username might already exist or WP error).";
                    }
                } else {
                    $errors = $validation;
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/doctors/create.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $doctor = $this->doctorRepo->find($id);

        if (!$doctor) {
            echo "Doctor not found.";
            return;
        }

        $errors = [];
        $data = (array) $doctor;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_doctor'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit_doctor_nonce')) {
                $postData = stripslashes_deep($_POST['doctor']);
                $validation = $this->validator->validate($postData);

                if ($validation === true) {
                    $result = $this->doctorService->updateDoctor($id, $postData);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-doctors&updated=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to update doctor.";
                    }
                } else {
                    $errors = $validation;
                    $data = array_merge($data, $postData); // keep user input
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/doctors/edit.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }
}
