<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use Dayuse\Istorija\EventStore\EventData;
use Dayuse\Istorija\EventStore\EventEnvelope;
use Dayuse\Istorija\EventStore\EventMetadata;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\SlicedReadResult;
use Dayuse\Istorija\Serializer\JsonObjectSerializer;
use Dayuse\Istorija\Utils\Contract;
use Dayuse\Istorija\Utils\Ensure;
use Verraes\ClassFunctions\ClassFunctions;

class EventStoreMessageTranslator implements DomainEventFactory, EventEnvelopeFactory
{
    private $serializer;

    public function __construct(JsonObjectSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function fromDomainEvents(DomainEventCollection $domainEvents, array $additionalMetadata = []): array
    {
        Ensure::keyNotExists($additionalMetadata, 'class');
        Ensure::keyNotExists($additionalMetadata, 'date');
        Ensure::keyNotExists($additionalMetadata, 'dateFormat');

        $eventEnvelopes = [];
        foreach ($domainEvents as $domainEvent) {
            $eventMetadata = array_merge($additionalMetadata, [
                'class'      => ClassFunctions::canonical($domainEvent),
                'date'       => (new \DateTime)->format(\DateTime::ATOM),
                'dateFormat' => \DateTime::ATOM,
            ]);

            $eventEnvelopes[] = EventEnvelope::wrap(
                Contract::canonicalFrom($domainEvent),
                new EventData($this->serializer->serialize($domainEvent), $this->serializer->getContentType()),
                new EventMetadata($this->serializer->serialize($eventMetadata), $this->serializer->getContentType())
            );
        }

        return $eventEnvelopes;
    }

    public function fromEventRecord(EventRecord $eventRecord): DomainEvent
    {
        $this->serializer->assertContentType($eventRecord->getMetadata()->getContentType());
        $this->serializer->assertContentType($eventRecord->getData()->getContentType());

        $metadata = $this->serializer->deserialize($eventRecord->getMetadata()->getPayload());

        return $this->serializer->deserialize(
            $eventRecord->getData()->getPayload(),
            $metadata['class']
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
