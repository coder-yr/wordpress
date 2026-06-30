<?php

namespace ClinicManagement\Controllers;

use ClinicManagement\Repositories\DoctorRepository;
use ClinicManagement\Services\AvailabilityService;

class DoctorController
{
    /**
     * @var DoctorRepository
     */
    protected $doctorRepo;

    /**
     * @var AvailabilityService
     */
    protected $availabilityService;

    public function __construct(DoctorRepository $doctorRepo, AvailabilityService $availabilityService)
    {
        $this->doctorRepo = $doctorRepo;
        $this->availabilityService = $availabilityService;
    }

    /**
     * Get all doctors
     * GET /doctors
     */
    public function index(\WP_REST_Request $request)
    {
        $doctors = $this->doctorRepo->all();
        
        return rest_ensure_response([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    /**
     * Get a doctor's available slots for a given date
     * GET /doctors/{id}/availability?date=YYYY-MM-DD
     */
    public function availability(\WP_REST_Request $request)
    {
        $doctorId = (int) $request->get_param('id');
        $date = $request->get_param('date');

        if (!$date) {
            return new \WP_Error('missing_params', 'The date parameter is required.', ['status' => 400]);
        }

        $doctor = $this->doctorRepo->find($doctorId);
        if (!$doctor) {
            return new \WP_Error('not_found', 'Doctor not found.', ['status' => 404]);
        }

        $slots = $this->availabilityService->getAvailableSlots($doctorId, $date);

        return rest_ensure_response([
            'status' => 'success',
            'data' => [
                'doctor_id' => $doctorId,
                'date' => $date,
                'available_slots' => $slots
            ]
        ]);
    }
}
