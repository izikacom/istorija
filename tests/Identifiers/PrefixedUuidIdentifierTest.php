<?php

namespace DayUse\Test\Istorija\Identifiers;


use DayUse\Istorija\Identifiers\PrefixedUuidIdentifier;
use PHPUnit\Framework\TestCase;

class PrefixedUuidIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_could_be_generated()
    {
        $identifier = ReservationId::generate();

        $this->assertNotNull($identifier);
    }

    /**
     * @test
     */
    public function it_could_be_serialized()
    {
        $identifier = ReservationId::generate();
        $other      = ReservationId::fromString((string)$identifier);

        $this->assertEquals($other, $identifier);
        $this->assertNotSame($other, $identifier);
    }

    /**
     * @test
     */
    public function from_string_must_be_prefixed()
    {
        $this->expectException(\InvalidArgumentException::class);

        ReservationId::fromString('b3dc7470-08f6-44ba-99ef-a28d288a9912');
    }

    /**
     * @test
     */
    public function from_string_must_be_correctly_prefixed()
    {
        $this->expectException(\InvalidArgumentException::class);

        ReservationId::fromString('booking-b3dc7470-08f6-44ba-99ef-a28d288a9912');
    }
}

class ReservationId extends PrefixedUuidIdentifier
{
    static protected function prefix()
    {
        return 'reservation';
    }
}
