<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventSourcing\DomainEvent;

use Dayuse\Istorija\EventSourcing\DomainEvent\EventDateTime;
use PHPUnit\Framework\TestCase;

class EventDateTimeTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_mock_now_is_possible()
    {
        $mockedNow = new \DateTimeImmutable('-3 days');

        $this->assertNotEquals($mockedNow->getTimestamp(), EventDateTime::now()->toDateTimeImmutable()->getTimestamp());

        EventDateTime::mockNow($mockedNow);

        $this->assertEquals($mockedNow->getTimestamp(), EventDateTime::now()->toDateTimeImmutable()->getTimestamp());
    }

    /**
     * @test
     */
    public function ensure_to_string_is_working()
    {
        $this->assertNotEmpty((string)EventDateTime::now());
    }

    /**
     * @test
     */
    public function ensure_from_string_is_working()
    {
        $this->assertNotEmpty(EventDateTime::fromString('2017-10-21T09:24:12.481962+00:00'));
        $this->assertNotEmpty(EventDateTime::fromString('2017-10-24T09:08:35.000000+00:00'));
    }
}
