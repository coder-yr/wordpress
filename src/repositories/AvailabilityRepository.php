<?php

namespace ClinicManagement\Repositories;

class AvailabilityRepository extends BaseRepository
{
    protected function setTable()
    {
        $this->table = $this->config->get('database.tables.availability');
    }
    
    public function getByDoctorId(int $doctorId)
    {
        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE doctor_id = %d", $doctorId);
        return $this->db->get_results($sql) ?: [];
    }
    
    public function updateAvailability(int $doctorId, int $dayOfWeek, string $startTime, string $endTime)
    {
        // Check if exists
        $sql = $this->db->prepare("SELECT id FROM {$this->table} WHERE doctor_id = %d AND day_of_week = %d", $doctorId, $dayOfWeek);
        $id = $this->db->get_var($sql);
        
        if ($id) {
            return $this->update((int) $id, [
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);
        } else {
            return $this->create([
                'doctor_id' => $doctorId,
                'day_of_week' => $dayOfWeek,
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);
        }
    }
}
