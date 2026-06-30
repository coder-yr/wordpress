<?php

namespace ClinicManagement\Services;

use ClinicManagement\Repositories\DoctorRepository;
use ClinicManagement\Events\Dispatcher;
use ClinicManagement\Events\DoctorCreated;
use ClinicManagement\Events\DoctorUpdated;

class DoctorService
{
    protected $doctorRepo;
    protected $dispatcher;

    public function __construct(DoctorRepository $doctorRepo, Dispatcher $dispatcher)
    {
        $this->doctorRepo = $doctorRepo;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create a new doctor and their WP user account.
     *
     * @param array $data
     * @return int|false The new doctor ID, or false on failure.
     */
    public function createDoctor(array $data)
    {
        // 1. Create WordPress User for the doctor
        $username = sanitize_user(strtolower($data['first_name'] . '.' . $data['last_name']));

        // Ensure username is unique
        $username_base = $username;
        $counter = 1;
        while (username_exists($username)) {
            $username = $username_base . $counter;
            $counter++;
        }

        $password = wp_generate_password();

        $userId = wp_insert_user([
            'user_login' => $username,
            'user_pass' => $password,
            'user_email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => 'clinic_doctor'
        ]);

        if (is_wp_error($userId)) {

            error_log('Doctor Creation Error Code: ' . $userId->get_error_code());
            error_log('Doctor Creation Error Message: ' . $userId->get_error_message());
            error_log(print_r($userId->get_error_data(), true));

            return false;
        }
        // 2. Map data for repository
        $doctorData = [
            'user_id' => $userId,
            'department_id' => $data['department_id'],
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'specialization' => sanitize_text_field($data['specialization'] ?? ''),
            'qualification' => sanitize_text_field($data['qualification'] ?? ''),
            'experience_years' => (int) ($data['experience_years'] ?? 0),
            'consultation_fee' => (float) $data['consultation_fee'],
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        // 3. Create doctor record
        $doctorId = $this->doctorRepo->create($doctorData);

        if ($doctorId) {
            // 4. Fire event
            $this->dispatcher->dispatch(new DoctorCreated($doctorId, $doctorData));
            return $doctorId;
        }

        return false;
    }

    /**
     * Update a doctor.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDoctor(int $id, array $data)
    {
        $doctor = $this->doctorRepo->find($id);
        if (!$doctor) {
            return false;
        }

        // 1. Update WP User if necessary (e.g., email or name changed)
        if (isset($data['email']) || isset($data['first_name']) || isset($data['last_name'])) {
            wp_update_user([
                'ID' => $doctor->user_id,
                'user_email' => $data['email'] ?? $doctor->email,
                'first_name' => $data['first_name'] ?? $doctor->first_name,
                'last_name' => $data['last_name'] ?? $doctor->last_name,
            ]);
        }

        // 2. Map data for repository
        $doctorData = [
            'department_id' => $data['department_id'],
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'specialization' => sanitize_text_field($data['specialization'] ?? ''),
            'qualification' => sanitize_text_field($data['qualification'] ?? ''),
            'experience_years' => (int) ($data['experience_years'] ?? 0),
            'consultation_fee' => (float) $data['consultation_fee'],
            'status' => sanitize_text_field($data['status'] ?? 'active')
        ];

        $updated = $this->doctorRepo->update($id, $doctorData);

        if ($updated !== false) {
            $this->dispatcher->dispatch(new DoctorUpdated($id, $doctorData));
            return true;
        }

        return false;
    }

    /**
     * Delete a doctor.
     *
     * @param int $id
     * @return bool
     */
    public function deleteDoctor(int $id)
    {
        $doctor = $this->doctorRepo->find($id);
        if (!$doctor) {
            return false;
        }

        // Delete WP User
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($doctor->user_id);

        return (bool) $this->doctorRepo->delete($id);
    }
}
