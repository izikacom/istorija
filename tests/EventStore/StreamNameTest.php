<?php

namespace Dayuse\Test\Istorija\EventStore;

use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Identifiers\GenericUuidIdentifier;
use Dayuse\Istorija\Utils\Contract;
use PHPUnit\Framework\TestCase;

class StreamNameTest extends TestCase
{
    /**
     * @test
     */
    public function it_could_not_be_constructed_with_contract_using_delimiter()
    {
        $this->expectException(\InvalidArgumentException::class);

        new StreamName(
            GenericUuidIdentifier::generate(),
            Contract::with('Reservation-Registered')
        );
    }

    /**
     * @test
     */
    public function it_should_return_canonical_name()
    {
        $streamName = new StreamName(
            GenericUuidIdentifier::fromString("a37b03fc-4c6c-4a8d-b579-a80bbaab49d2"),
            Contract::with('Reservation.Registered')
        );

        $this->assertEquals(
            "Reservation.Registered-a37b03fc-4c6c-4a8d-b579-a80bbaab49d2",
            $streamName
        );
    }

    /**
     * @test
     */
    public function it_should_be_constructed_using_string()
    {
        $string = "App.Domain.Reservation.Reservation-reservation-28401629-134a-4211-bd78-dd5d4fbb1ecd";

        $streamName = StreamName::fromString($string);

        $this->assertNotNull($streamName);
        $this->assertEquals("reservation-28401629-134a-4211-bd78-dd5d4fbb1ecd", (string)$streamName->getIdentifier());
        $this->assertEquals("App.Domain.Reservation.Reservation", $streamName->getContract());
    }
}
