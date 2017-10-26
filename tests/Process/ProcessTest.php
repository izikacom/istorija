<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Test\Istorija\Process;

use DayUse\Istorija\Identifiers\PrefixedIdentifier;
use DayUse\Istorija\Process\AutoNameProcessTrait;
use DayUse\Istorija\Process\Process;
use PHPUnit\Framework\TestCase;

class ProcessTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_get_process_id()
    {
        $process   = new RegisterReservationProcess();
        $processId = $process->getProcessIdFromAggregate(BookingAttemptId::generate());

        $this->assertEquals('process-register-reservation-booking-attempt-id', (string)$processId);
    }
}

class BookingAttemptId extends PrefixedIdentifier
{
    static protected function prefix()
    {
        return 'booking-attempt';
    }

    public static function generate()
    {
        return parent::fromString(static::prefix() . self::SEPARATOR . 'id');
    }
}

class RegisterReservationProcess extends Process
{
    use AutoNameProcessTrait;
}