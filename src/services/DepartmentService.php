<?php

namespace ClinicManagement\Services;

use ClinicManagement\Repositories\DepartmentRepository;

class DepartmentService
{
    protected $departmentRepo;

    public function __construct(DepartmentRepository $departmentRepo)
    {
        $this->departmentRepo = $departmentRepo;
    }

    public function createDepartment(array $data)
    {
        $departmentData = [
            'name' => sanitize_text_field($data['name']),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        return $this->departmentRepo->create($departmentData);
    }

    public function updateDepartment(int $id, array $data)
    {
        $departmentData = [
            'name' => sanitize_text_field($data['name']),
            'description' => sanitize_textarea_field($data['description'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        return $this->departmentRepo->update($id, $departmentData);
    }

    public function deleteDepartment(int $id)
    {
        return $this->departmentRepo->delete($id);
    }
}
