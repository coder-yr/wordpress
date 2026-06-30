<?php

namespace ClinicManagement\Repositories;

class LeaveRepository extends BaseRepository
{
    protected function setTable()
    {
        $this->table = $this->config->get('database.tables.leaves');
    }

    public function getByDoctorId(int $doctorId)
    {
        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE doctor_id = %d ORDER BY date ASC", $doctorId);
        return $this->db->get_results($sql) ?: [];
    }
}
