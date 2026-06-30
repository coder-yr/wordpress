<?php

namespace ClinicManagement\Validators;

class PatientValidator
{
    public function validate(array $data)
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        if (empty($data['email']) || !is_email($data['email'])) {
            $errors['email'] = 'A valid email is required.';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required.';
        }

        if (empty($data['date_of_birth'])) {
            $errors['date_of_birth'] = 'Date of birth is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
