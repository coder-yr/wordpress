<?php

namespace ClinicManagement\Validators;

class DepartmentValidator
{
    public function validate(array $data)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Department name is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
