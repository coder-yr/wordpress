<?php

namespace ClinicManagement\Validators;

class DoctorValidator
{
    /**
     * Validate data for creating or updating a doctor.
     *
     * @param array $data
     * @return array|bool Array of error messages, or true if valid.
     */
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

        if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
            $errors['department_id'] = 'Valid department is required.';
        }

        if (empty($data['consultation_fee']) || !is_numeric($data['consultation_fee'])) {
            $errors['consultation_fee'] = 'Valid consultation fee is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
