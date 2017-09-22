<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 21/09/2017
 * Time: 14:55
 */

namespace DayUse\Istorija\Identifiers;

use PHPUnit\Framework\TestCase;

final class PrefixedUuidIdentifierTest extends TestCase
{
    public function testMatchingPattern(): void
    {
        $this->assertTrue(ReservationId::isMatchingPattern('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729'));
        $this->assertTrue(ReservationId::isMatchingPattern('reservation-0d9edee8-5d8e-486b-879b-a6c54d752729'));
        $this->assertFalse(ReservationId::isMatchingPattern('reservation-not-uuid'));
        $this->assertFalse(ReservationId::isMatchingPattern('user-0d9edee8-5d8e-486b-879b-a6c54d752729'));
    }

    public function testGeneration(): void
    {
        $this->assertInstanceOf(ReservationId::class, ReservationId::generate());
    }

    public function testAssertionFromStringFactory(): void
    {
        $this->assertEquals('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729', (string)ReservationId::fromString('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729'));

        $this->expectException(\InvalidArgumentException::class);
        ReservationId::fromString('user-0D9EDEE8-5D8E-486B-879B-A6C54D752729');
    }
}

class ReservationId extends PrefixedUuidIdentifier
{
    static protected function prefix()
    {
        return 'reservation';
    }
}