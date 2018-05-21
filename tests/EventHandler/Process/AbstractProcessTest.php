<?php

namespace Dayuse\Test\Istorija\EventHandler\Process;

use Dayuse\Istorija\Identifiers\PrefixedIdentifier;
use Dayuse\Istorija\EventHandler\Process\AbstractProcess;
use PHPUnit\Framework\TestCase;

class AbstractProcessTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_get_process_id(): void
    {
        $process = new RegisterReservationProcess();
        $processId = $process->getProcessId(BookingAttemptId::generate());

        $this->assertEquals('process-register-reservation-booking-attempt-id', (string) $processId);
    }

    /**
     * @test
     */
    public function ensure_name_is_correct(): void
    {
        $process = new RegisterReservationProcess();

        $this->assertEquals('register-reservation', $process->getName());
    }

    /**
     * @test
     */
    public function ensure_class_name_end_with_process(): void
    {
        $process = new WrongName();

        $this->expectException(\InvalidArgumentException::class);

        $process->getName();
    }
}

class BookingAttemptId extends PrefixedIdentifier
{
    protected static function prefix(): string
    {
        return 'booking-attempt';
    }

    public static function generate()
    {
        return parent::fromString(static::prefix() . self::SEPARATOR . 'id');
    }
}

class RegisterReservationProcess extends AbstractProcess
{
}

class WrongName extends AbstractProcess
{
}
