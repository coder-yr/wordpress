<?php

namespace ClinicManagement\Services\Availability;

use ClinicManagement\Repositories\AppointmentRepository;

class ConflictChecker
{
    /**
     * @var AppointmentRepository
     */
    protected $appointmentRepo;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepo = $appointmentRepo;
    }

    /**
     * Remove booked slots from the generated list.
     *
     * @param int $doctorId
     * @param string $date
     * @param array $slots
     * @return array
     */
    public function filterAvailableSlots(int $doctorId, string $date, array $slots): array
    {
        // Get all appointments for this doctor on this day
        $appointments = $this->appointmentRepo->getDoctorAppointmentsOnDate($doctorId, $date);

        $bookedTimes = array_map(function ($apt) {
            // Convert '09:30:00' to '09:30'
            return substr($apt->start_time, 0, 5);
        }, $appointments);

        // Filter out slots that are already booked
        return array_values(array_filter($slots, function ($slot) use ($bookedTimes) {
            return !in_array($slot, $bookedTimes);
        }));
    }

    /**
     * Verify if a single slot is available right before booking.
     *
     * @param int $doctorId
     * @param string $date
     * @param string $startTime
     * @return bool
     */
    public function isAvailable(int $doctorId, string $date, string $startTime): bool
    {
        return !$this->appointmentRepo->isSlotBooked($doctorId, $date, $startTime);
    }
}
