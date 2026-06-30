<?php

namespace ClinicManagement\Services;

use ClinicManagement\Repositories\AppointmentRepository;

class AppointmentAdminService
{
    protected $appointmentRepo;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepo = $appointmentRepo;
    }

    public function updateStatus(int $id, string $status)
    {
        $validStatuses = ['pending', 'approved', 'completed', 'cancelled', 'no_show'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->appointmentRepo->update($id, ['status' => $status]);
    }

    public function reschedule(int $id, string $newDate, string $newTime)
    {
        return $this->appointmentRepo->update($id, [
            'appointment_date' => sanitize_text_field($newDate),
            'appointment_time' => sanitize_text_field($newTime),
            'status' => 'approved' // Rescheduling implies approval
        ]);
    }

    public function deleteAppointment(int $id)
    {
        return $this->appointmentRepo->delete($id);
    }
}
