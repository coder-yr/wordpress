<?php

namespace ClinicManagement\Services\Availability;

class SlotGenerator
{
    /**
     * Generate time slots based on working hours and slot duration.
     *
     * @param string $startTime (H:i)
     * @param string $endTime (H:i)
     * @param int $durationMins
     * @param string|null $breakStart
     * @param string|null $breakEnd
     * @return array
     */
    public function generate(string $startTime, string $endTime, int $durationMins, ?string $breakStart = null, ?string $breakEnd = null): array
    {
        $slots = [];
        
        $current = strtotime($startTime);
        $end = strtotime($endTime);
        
        $bStart = $breakStart ? strtotime($breakStart) : null;
        $bEnd = $breakEnd ? strtotime($breakEnd) : null;

        while ($current < $end) {
            $slotEnd = strtotime("+$durationMins minutes", $current);
            
            // Check if slot falls into the break time
            $isDuringBreak = false;
            if ($bStart && $bEnd) {
                if (($current >= $bStart && $current < $bEnd) || ($slotEnd > $bStart && $slotEnd <= $bEnd)) {
                    $isDuringBreak = true;
                }
            }

            if (!$isDuringBreak && $slotEnd <= $end) {
                $slots[] = date('H:i', $current);
            }

            $current = $slotEnd;
        }

        return $slots;
    }
}
