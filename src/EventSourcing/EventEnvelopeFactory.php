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
use DayUse\Istorija\EventStore\EventMetadata;
use DayUse\Istorija\Utils\Contract;
use Verraes\ClassFunctions\ClassFunctions;

class EventEnvelopeFactory
{
    /**
     * @var JsonObjectSerializer
     */
    private $serializer;

    /**
     * EventEnvelopeFactory constructor.
     *
     * @param JsonObjectSerializer $serializer
     */
    public function __construct(JsonObjectSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param DomainEventCollection $domainEvents
     * @param array                 $context
     *
     * @return array
     */
    public function fromDomainEvents(DomainEventCollection $domainEvents, array $context): array
    {
        $eventEnvelopes = [];
        foreach ($domainEvents as $domainEvent) {
            $eventMetadata = array_merge($context, [
                'typeHint' => ClassFunctions::canonical($domainEvent),
            ]);

            $eventEnvelopes[] = EventEnvelope::wrap(
                Contract::canonicalFrom($domainEvent),
                new EventData($this->serializer->serialize($domainEvent), $this->serializer->getContentType()),
                new EventMetadata($this->serializer->serialize($eventMetadata), $this->serializer->getContentType())
            );
        }

        return $eventEnvelopes;
    }
}