<?php

namespace ClinicManagement\Events;

class AppointmentBooked
{
    /**
     * @var int
     */
    public $appointmentId;

    /**
     * @var array
     */
    public $data;

    public function __construct(int $appointmentId, array $data)
    {
        $this->appointmentId = $appointmentId;
        $this->data = $data;
    }
}
