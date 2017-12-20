<?php

namespace Dayuse\Test\Istorija\Identifiers;


use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;
use PHPUnit\Framework\TestCase;

class PrefixedUuidIdentifierTest extends TestCase
{
    public function it_should_match_pattern(): void
    {
        $this->assertTrue(ReservationId::isMatchingPattern('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729'));
        $this->assertTrue(ReservationId::isMatchingPattern('reservation-0d9edee8-5d8e-486b-879b-a6c54d752729'));
        $this->assertFalse(ReservationId::isMatchingPattern('reservation-not-uuid'));
        $this->assertFalse(ReservationId::isMatchingPattern('user-0d9edee8-5d8e-486b-879b-a6c54d752729'));
    }

    public function it_should_be_generated(): void
    {
        $this->assertInstanceOf(ReservationId::class, ReservationId::generate());
    }

    public function it_should_be_serialized(): void
    {
        $this->assertEquals('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729', (string)ReservationId::fromString('reservation-0D9EDEE8-5D8E-486B-879B-A6C54D752729'));

        $this->expectException(\InvalidArgumentException::class);
        ReservationId::fromString('user-0D9EDEE8-5D8E-486B-879B-A6C54D752729');
    }

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
    protected static function prefix()
    {
        return 'reservation';
    }
}
