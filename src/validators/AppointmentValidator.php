<?php

namespace ClinicManagement\Validators;

class AppointmentValidator
{
    public function validateReschedule(array $data)
    {
        $errors = [];

        if (empty($data['appointment_date'])) {
            $errors['appointment_date'] = 'Date is required.';
        }

        if (empty($data['appointment_time'])) {
            $errors['appointment_time'] = 'Time is required.';
        }

        return empty($errors) ? true : $errors;
    }
}
