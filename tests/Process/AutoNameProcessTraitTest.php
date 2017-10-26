<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Test\Istorija\Process;

use DayUse\Istorija\Process\AutoNameProcessTrait;
use DayUse\Istorija\Process\Process;
use PHPUnit\Framework\TestCase;

class AutoNameProcessTraitTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_name_is_correct()
    {
        $process = new BookingAttemptProcess();

        $this->assertEquals('booking-attempt', $process->getName());
    }

    /**
     * @test
     */
    public function ensure_class_name_end_with_process()
    {
        $process = new WrongName();

        $this->expectException(\InvalidArgumentException::class);

        $process->getName();
    }
}

class BookingAttemptProcess extends Process
{
    use AutoNameProcessTrait;
}

class WrongName extends Process
{
    use AutoNameProcessTrait;
}