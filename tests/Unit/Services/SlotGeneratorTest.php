<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use ClinicManagement\Services\Availability\SlotGenerator;

class SlotGeneratorTest extends TestCase
{
    public function test_it_generates_correct_number_of_slots()
    {
        $generator = new SlotGenerator();
        
        // 09:00 to 11:00 with 30 min duration = 4 slots
        $slots = $generator->generate('09:00', '11:00', 30);
        
        $this->assertCount(4, $slots);
        $this->assertEquals(['09:00', '09:30', '10:00', '10:30'], $slots);
    }

    public function test_it_skips_breaks()
    {
        $generator = new SlotGenerator();
        
        // 09:00 to 12:00, 60 mins. Break from 10:00 to 11:00
        $slots = $generator->generate('09:00', '12:00', 60, '10:00', '11:00');
        
        // Should get 09:00 and 11:00
        $this->assertCount(2, $slots);
        $this->assertEquals(['09:00', '11:00'], $slots);
    }
}
