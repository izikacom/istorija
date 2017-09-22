<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 21/09/2017
 * Time: 16:03
 */

namespace DayUse\Istorija\EventSourcing;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use DayUse\Istorija\EventStore\EventData;
use DayUse\Istorija\EventStore\EventEnvelope;
use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\Utils\Contract;

class EventEnvelopeFactory
{
    /**
     * @param DomainEventCollection $domainEvents
     *
     * @return EventRecord[]
     */
    public function fromDomainEvents(DomainEventCollection $domainEvents): array
    {
        $eventEnvelopes = [];
        foreach ($domainEvents as $domainEvent) {
            // get data from serialized version of $domainEvent
            $eventData     = new EventData(json_encode(['id' => '123', 'username' => sprintf('Boris-%d', $i)]), 'application/json');
            $eventMetadata = null;

            $eventEnvelopes[] = EventEnvelope::wrap(
                Contract::canonicalFrom($domainEvent),
                $eventData,
                $eventMetadata
            );
        }

        return $eventEnvelopes;
    }
}