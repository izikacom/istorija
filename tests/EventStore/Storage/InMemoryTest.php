<?php

namespace Dayuse\Test\Istorija\EventStore\Storage;


use Dayuse\Istorija\EventStore\Configuration;
use Dayuse\Istorija\EventStore\EventData;
use Dayuse\Istorija\EventStore\EventEnvelope;
use Dayuse\Istorija\EventStore\EventMetadata;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\EventStore;
use Dayuse\Istorija\EventStore\ExpectedVersion;
use Dayuse\Istorija\EventStore\Storage\InMemory;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Identifiers\GenericUuidIdentifier;
use Dayuse\Istorija\Utils\Contract;
use PHPUnit\Framework\TestCase;

class InMemoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_deal_with_simple_scenario()
    {
        $eventStore = new EventStore(new Configuration(new InMemory()));

        // Generate a bunch of raw events
        $events = [];
        for ($i = 0; $i <= 4; ++$i) {
            $events[] = EventEnvelope::wrap(
                Contract::with('OrderPlaced'),
                new EventData(json_encode(['id' => $i, 'username' => sprintf('Boris-%d', $i)]), 'application/json'),
                new EventMetadata(json_encode(['actor' => 'System Administrator']), 'application/json')
            );
        }

        $streamA = new StreamName(GenericUuidIdentifier::fromString('11111111-0999-11e7-bdb3-024b86486f93'), Contract::with('Order'));
        $streamB = new StreamName(GenericUuidIdentifier::fromString('22222222-0999-11e7-bdb3-024b86486f92'), Contract::with('Order'));

        $eventStore->append($streamA, ExpectedVersion::ANY, $events);
        $eventStore->append($streamB, ExpectedVersion::ANY, $events);

        $this->assertCount(10, $eventStore->readAllEvents());
        $this->assertCount(5, $eventStore->readStreamEventsForward($streamA));
        $this->assertCount(5, $eventStore->readStreamEventsForward($streamB));

        $eventStore->delete($streamB, ExpectedVersion::ANY);

        $this->assertCount(5, $eventStore->readAllEvents());
        $this->assertCount(5, $eventStore->readStreamEventsForward($streamA));

        $eventStore->delete($streamA, ExpectedVersion::ANY);

        $this->assertCount(0, $eventStore->readAllEvents());
    }
}

