<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use ClinicManagement\Services\BookingService;
use ClinicManagement\Repositories\AppointmentRepository;
use ClinicManagement\Services\Availability\ConflictChecker;
use ClinicManagement\Events\Dispatcher;

class BookingServiceTest extends TestCase
{
    public function test_it_fails_when_slot_is_unavailable()
    {
        // Mock ConflictChecker to return false (unavailable)
        $conflictChecker = $this->createMock(ConflictChecker::class);
        $conflictChecker->method('isAvailable')->willReturn(false);

        $repo = $this->createMock(AppointmentRepository::class);
        $dispatcher = $this->createMock(Dispatcher::class);

        $service = new BookingService($repo, $conflictChecker, $dispatcher);

        $result = $service->bookAppointment([
            'doctor_id' => 1,
            'appointment_date' => '2026-07-01',
            'start_time' => '10:00'
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals('This slot is already booked or unavailable.', $result['message']);
    }
}
