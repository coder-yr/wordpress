<?php

namespace ClinicManagement\Controllers;

use ClinicManagement\Validators\Validator;
use ClinicManagement\Services\BookingService;
use ClinicManagement\Repositories\AppointmentRepository;

class AppointmentController
{
    /**
     * @var BookingService
     */
    protected $bookingService;

    /**
     * @var AppointmentRepository
     */
    protected $appointmentRepo;

    public function __construct(BookingService $bookingService, AppointmentRepository $appointmentRepo)
    {
        $this->bookingService = $bookingService;
        $this->appointmentRepo = $appointmentRepo;
    }

    /**
     * Book a new appointment
     * POST /appointments
     */
    public function store(\WP_REST_Request $request)
    {
        $data = $request->get_params();

        // Auto-assign patient_id if missing and user is logged in
        if (!isset($data['patient_id'])) {
            $patientRepo = \ClinicManagement\Bootstrap\Application::getInstance()->make(\ClinicManagement\Repositories\PatientRepository::class);
            $patient = $patientRepo->findByUserId(get_current_user_id());
            if ($patient) {
                $data['patient_id'] = $patient->id;
            }
        }

        // Validate the request
        $validator = Validator::make($data, [
            'doctor_id'        => 'required|numeric',
            'department_id'    => 'required|numeric',
            'patient_id'       => 'required|numeric',
            'appointment_date' => 'required|date',
            'start_time'       => 'required'
        ]);

        if (!$validator->passes()) {
            return new \WP_Error('validation_error', 'Invalid input data.', [
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        }

        // Delegate to Service
        $result = $this->bookingService->bookAppointment($data);

        if (!$result['success']) {
            return new \WP_Error('booking_failed', $result['message'], ['status' => 409]);
        }

        return rest_ensure_response([
            'status' => 'success',
            'message' => $result['message'],
            'data' => [
                'appointment_id' => $result['appointment_id'],
                'token' => $result['token'],
                'meeting_link' => $result['meeting_link'] // In production, don't expose this until 10 mins before
            ]
        ]);
    }

    /**
     * View an appointment detail
     * GET /appointments/{id}
     */
    public function show(\WP_REST_Request $request)
    {
        $id = (int) $request->get_param('id');
        $appointment = $this->appointmentRepo->find($id);

        if (!$appointment) {
            return new \WP_Error('not_found', 'Appointment not found.', ['status' => 404]);
        }

        return rest_ensure_response([
            'status' => 'success',
            'data' => $appointment
        ]);
    }
}
