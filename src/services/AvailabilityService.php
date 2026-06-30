<?php

namespace ClinicManagement\Services;

use ClinicManagement\Services\Availability\SlotGenerator;
use ClinicManagement\Services\Availability\ConflictChecker;

class AvailabilityService
{
    /**
     * @var SlotGenerator
     */
    protected $slotGenerator;

    /**
     * @var ConflictChecker
     */
    protected $conflictChecker;

    /**
     * @var \wpdb
     */
    protected $db;

    public function __construct(SlotGenerator $slotGenerator, ConflictChecker $conflictChecker)
    {
        $this->slotGenerator = $slotGenerator;
        $this->conflictChecker = $conflictChecker;
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Get available slots for a doctor on a specific date.
     *
     * @param int $doctorId
     * @param string $date (Y-m-d)
     * @return array
     */
    public function getAvailableSlots(int $doctorId, string $date): array
    {
        // 1. Determine Day of Week (0=Sun, 6=Sat)
        $dayOfWeek = date('w', strtotime($date));

        // 2. Fetch Working Hours
        $availabilityTable = $this->db->prefix . 'clinic_availability';
        $workingHours = $this->db->get_row($this->db->prepare(
            "SELECT * FROM {$availabilityTable} WHERE doctor_id = %d AND day_of_week = %d LIMIT 1",
            $doctorId,
            $dayOfWeek
        ));

        if (!$workingHours) {
            return []; // Doctor doesn't work on this day
        }

        // 3. Fetch Leaves (Is the doctor on holiday?)
        $leavesTable = $this->db->prefix . 'clinic_leaves';
        $isOnLeave = $this->db->get_var($this->db->prepare(
            "SELECT id FROM {$leavesTable} WHERE doctor_id = %d AND date = %s AND status = 'approved' LIMIT 1",
            $doctorId,
            $date
        ));

        if ($isOnLeave) {
            return []; // Doctor is on leave
        }

        // 4. Generate Raw Slots
        $rawSlots = $this->slotGenerator->generate(
            $workingHours->start_time,
            $workingHours->end_time,
            (int) $workingHours->slot_duration_mins,
            $workingHours->break_start,
            $workingHours->break_end
        );

        // 5. Filter out booked slots
        $availableSlots = $this->conflictChecker->filterAvailableSlots($doctorId, $date, $rawSlots);

        return $availableSlots;
    }
}
