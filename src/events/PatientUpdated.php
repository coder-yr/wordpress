<?php

namespace ClinicManagement\Events;

class PatientUpdated
{
    public $patientId;
    public $data;

    public function __construct(int $patientId, array $data)
    {
        $this->patientId = $patientId;
        $this->data = $data;
    }
}
