<?php

namespace ClinicManagement\Events;

class DoctorUpdated
{
    public $doctorId;
    public $data;

    public function __construct(int $doctorId, array $data)
    {
        $this->doctorId = $doctorId;
        $this->data = $data;
    }
}
