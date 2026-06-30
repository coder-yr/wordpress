<?php

namespace ClinicManagement\Repositories;

class PatientRepository extends BaseRepository
{
    protected function setTable()
    {
        $this->table = $this->config->get('database.tables.patients');
    }

    /**
     * Find a patient by their WP user ID.
     *
     * @param int $userId
     * @return object|null
     */
    public function findByUserId(int $userId)
    {
        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = %d LIMIT 1", $userId);
        return $this->db->get_row($sql);
    }
}
