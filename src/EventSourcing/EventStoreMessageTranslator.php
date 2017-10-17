<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 17/10/2017
 * Time: 16:36
 */

namespace DayUse\Istorija\EventSourcing;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use DayUse\Istorija\EventStore\EventData;
use DayUse\Istorija\EventStore\EventEnvelope;
use DayUse\Istorija\EventStore\EventMetadata;
use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\EventStore\SlicedReadResult;
use DayUse\Istorija\Serializer\JsonObjectSerializer;
use DayUse\Istorija\Utils\Contract;
use Verraes\ClassFunctions\ClassFunctions;

class EventStoreMessageTranslator implements DomainEventFactory, EventEnvelopeFactory
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
     *
     * @return array
     */
    public function fromDomainEvents(DomainEventCollection $domainEvents): array
    {
        $eventEnvelopes = [];
        foreach ($domainEvents as $domainEvent) {
            $eventMetadata = [
                'typeHint'   => ClassFunctions::canonical($domainEvent),
                'date'       => (new \DateTime)->format(\DateTime::ATOM),
                'dateFormat' => \DateTime::ATOM,
            ];

            $eventEnvelopes[] = EventEnvelope::wrap(
                Contract::canonicalFrom($domainEvent),
                new EventData($this->serializer->serialize($domainEvent), $this->serializer->getContentType()),
                new EventMetadata($this->serializer->serialize($eventMetadata), $this->serializer->getContentType())
            );
        }

        return $eventEnvelopes;
    }

    /**
     * @param EventRecord $eventRecord
     *
     * @return DomainEvent
     */
    public function fromEventRecord(EventRecord $eventRecord): DomainEvent
    {
        $this->serializer->assertContentType($eventRecord->getMetadata()->getContentType());
        $this->serializer->assertContentType($eventRecord->getData()->getContentType());

        $metadata = $this->serializer->deserialize($eventRecord->getMetadata()->getPayload());

        return $this->serializer->deserialize(
            $eventRecord->getData()->getPayload(),
            $metadata['typeHint']
        );
    }

    /**
     * @param SlicedReadResult $slicedReadResult
     *
     * @return DomainEventCollection
     */
    public function fromEventRecords(SlicedReadResult $slicedReadResult): DomainEventCollection
    {
        $events = [];

        foreach ($slicedReadResult as $eventRecord) {
            $events[] = $this->fromEventRecord($eventRecord);
        }

        return new DomainEventCollection($events);
    }
}