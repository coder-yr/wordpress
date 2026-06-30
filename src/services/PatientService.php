<?php

namespace ClinicManagement\Services;

use ClinicManagement\Repositories\PatientRepository;
use ClinicManagement\Events\Dispatcher;
use ClinicManagement\Events\PatientCreated;
use ClinicManagement\Events\PatientUpdated;

class PatientService
{
    protected $patientRepo;
    protected $dispatcher;

    public function __construct(PatientRepository $patientRepo, Dispatcher $dispatcher)
    {
        $this->patientRepo = $patientRepo;
        $this->dispatcher = $dispatcher;
    }

    public function createPatient(array $data)
    {
        $username = sanitize_user(strtolower($data['first_name'] . '.' . $data['last_name']));
        
        $username_base = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $username_base . $counter;
            $counter++;
        }

        $password = wp_generate_password();
        
        $userId = wp_insert_user([
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'role'       => 'clinic_patient'
        ]);

        if (is_wp_error($userId)) {
            return false;
        }

        $patientData = [
            'user_id' => $userId,
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'gender' => sanitize_text_field($data['gender'] ?? 'other'),
            'date_of_birth' => sanitize_text_field($data['date_of_birth'] ?? ''),
            'blood_group' => sanitize_text_field($data['blood_group'] ?? ''),
            'address' => sanitize_textarea_field($data['address'] ?? ''),
            'emergency_contact' => sanitize_text_field($data['emergency_contact'] ?? ''),
            'medical_history' => sanitize_textarea_field($data['medical_history'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        $patientId = $this->patientRepo->create($patientData);

        if ($patientId) {
            $this->dispatcher->dispatch(new PatientCreated($patientId, $patientData));
            return $patientId;
        }

        return false;
    }

    public function updatePatient(int $id, array $data)
    {
        $patient = $this->patientRepo->find($id);
        if (!$patient) {
            return false;
        }

        if (isset($data['email']) || isset($data['first_name']) || isset($data['last_name'])) {
            wp_update_user([
                'ID' => $patient->user_id,
                'user_email' => $data['email'] ?? $patient->email,
                'first_name' => $data['first_name'] ?? $patient->first_name,
                'last_name'  => $data['last_name'] ?? $patient->last_name,
            ]);
        }

        $patientData = [
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'gender' => sanitize_text_field($data['gender'] ?? 'other'),
            'date_of_birth' => sanitize_text_field($data['date_of_birth'] ?? ''),
            'blood_group' => sanitize_text_field($data['blood_group'] ?? ''),
            'address' => sanitize_textarea_field($data['address'] ?? ''),
            'emergency_contact' => sanitize_text_field($data['emergency_contact'] ?? ''),
            'medical_history' => sanitize_textarea_field($data['medical_history'] ?? ''),
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        $updated = $this->patientRepo->update($id, $patientData);

        if ($updated !== false) {
            $this->dispatcher->dispatch(new PatientUpdated($id, $patientData));
            return true;
        }

        return false;
    }

    public function deletePatient(int $id)
    {
        $patient = $this->patientRepo->find($id);
        if (!$patient) {
            return false;
        }

        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($patient->user_id);

        return (bool) $this->patientRepo->delete($id);
    }
}
