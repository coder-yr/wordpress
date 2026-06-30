<?php

namespace ClinicManagement\Repositories;

class DepartmentRepository extends BaseRepository
{
    protected function setTable()
    {
        $this->table = $this->config->get('database.tables.departments');
    }
}
