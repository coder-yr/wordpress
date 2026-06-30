<?php

namespace ClinicManagement\Events;

class PatientCreated
{
    public $patientId;
    public $data;

    public function __construct(int $patientId, array $data)
    {
        $this->patientId = $patientId;
        $this->data = $data;
    }
}
