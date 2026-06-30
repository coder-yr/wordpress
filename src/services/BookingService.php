<?php

namespace ClinicManagement\Services;

use ClinicManagement\Repositories\AppointmentRepository;
use ClinicManagement\Services\Availability\ConflictChecker;

use ClinicManagement\Events\Dispatcher;
use ClinicManagement\Events\AppointmentBooked;

class BookingService
{
    /**
     * @var AppointmentRepository
     */
    protected $appointmentRepo;

    /**
     * @var ConflictChecker
     */
    protected $conflictChecker;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(AppointmentRepository $appointmentRepo, ConflictChecker $conflictChecker, Dispatcher $dispatcher)
    {
        $this->appointmentRepo = $appointmentRepo;
        $this->conflictChecker = $conflictChecker;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Attempt to book an appointment.
     *
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'appointment_id' => int|null]
     */
    public function bookAppointment(array $data): array
    {
        $doctorId = (int) $data['doctor_id'];
        $date = $data['appointment_date'];
        $startTime = $data['start_time'];

        // 1. Final Concurrency/Conflict Check
        if (!$this->conflictChecker->isAvailable($doctorId, $date, $startTime)) {
            return [
                'success' => false,
                'message' => 'This slot is already booked or unavailable.'
            ];
        }

        // Calculate end time (assuming standard slot duration, or fetch from DB. Hardcoding 15 mins for example)
        $endTime = date('H:i:s', strtotime("+15 minutes", strtotime($startTime)));
        
        // Generate a random token
        $token = 'APT-' . strtoupper(substr(uniqid(), -5));

        // Generate Jitsi Room
        $meetingLink = 'Clinic-' . wp_generate_uuid4();

        // 2. Insert into DB
        $appointmentId = $this->appointmentRepo->create([
            'patient_id'       => $data['patient_id'],
            'doctor_id'        => $doctorId,
            'department_id'    => $data['department_id'],
            'appointment_date' => $date,
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'status'           => 'confirmed', // or pending depending on business logic
            'reason'           => $data['reason'] ?? '',
            'meeting_link'     => $meetingLink,
            'token_number'     => $token
        ]);

        if (!$appointmentId) {
            return [
                'success' => false,
                'message' => 'Failed to book appointment due to a database error.'
            ];
        }

        // Add additional generated data for the event listeners
        $data['token'] = $token;
        $data['meeting_link'] = $meetingLink;

        // Dispatch Event
        $this->dispatcher->dispatch(new AppointmentBooked($appointmentId, $data));

        return [
            'success' => true,
            'message' => 'Appointment booked successfully.',
            'appointment_id' => $appointmentId,
            'token' => $token,
            'meeting_link' => $meetingLink
        ];
    }
}
