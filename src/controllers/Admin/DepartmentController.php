<?php

namespace ClinicManagement\Controllers\Admin;

use ClinicManagement\Repositories\DepartmentRepository;
use ClinicManagement\Services\DepartmentService;
use ClinicManagement\Validators\DepartmentValidator;
use ClinicManagement\Classes\DepartmentListTable;

class DepartmentController
{
    protected $departmentRepo;
    protected $departmentService;
    protected $validator;

    public function __construct(
        DepartmentRepository $departmentRepo,
        DepartmentService $departmentService,
        DepartmentValidator $validator
    ) {
        $this->departmentRepo = $departmentRepo;
        $this->departmentService = $departmentService;
        $this->validator = $validator;
    }

    public function index()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_department_' . $id)) {
                $this->departmentService->deleteDepartment($id);
                echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-departments&deleted=1')) . "');</script>";
                exit;
            }
        }

        $listTable = new DepartmentListTable($this->departmentRepo);
        $listTable->prepare_items();
        
        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/departments/index.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function create()
    {
        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_department'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'create_department_nonce')) {
                $data = stripslashes_deep($_POST['department']);
                $validation = $this->validator->validate($data);

                if ($validation === true) {
                    $result = $this->departmentService->createDepartment($data);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-departments&created=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to create department.";
                    }
                } else {
                    $errors = $validation;
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/departments/create.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $department = $this->departmentRepo->find($id);

        if (!$department) {
            echo "Department not found.";
            return;
        }

        $errors = [];
        $data = (array) $department;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_department'])) {
            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'edit_department_nonce')) {
                $postData = stripslashes_deep($_POST['department']);
                $validation = $this->validator->validate($postData);

                if ($validation === true) {
                    $result = $this->departmentService->updateDepartment($id, $postData);
                    if ($result) {
                        echo "<script>window.location.replace('" . \esc_url_raw(admin_url('admin.php?page=clinic-departments&updated=1')) . "');</script>";
                        exit;
                    } else {
                        $errors[] = "Failed to update department.";
                    }
                } else {
                    $errors = $validation;
                    $data = array_merge($data, $postData);
                }
            } else {
                $errors[] = 'Nonce verification failed.';
            }
        }

        $viewPath = CLINIC_PLUGIN_DIR . 'views/admin/departments/edit.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        }
    }
}
