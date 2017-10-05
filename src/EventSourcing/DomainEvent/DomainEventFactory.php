<?php

namespace DayUse\Istorija\EventSourcing\DomainEvent;

use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\EventStore\SlicedReadResult;
use DayUse\Istorija\Serializer\JsonObjectSerializer;

class DomainEventFactory
{
    /**
     * @var JsonObjectSerializer
     */
    private $serializer;

    /**
     * DomainEventFactory constructor.
     *
     * @param JsonObjectSerializer $serializer
     */
    public function __construct(JsonObjectSerializer $serializer)
    {
        $this->serializer = $serializer;
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