<?php

namespace DayUse\Istorija\EventSourcing\DomainEvent;

use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\EventStore\SlicedReadResult;

class DomainEventFactory
{
    /**
     * @param EventRecord $eventRecord
     *
     * @return DomainEvent
     */
    public function fromEventRecord(EventRecord $eventRecord): DomainEvent
    {
        return new DomainEventDummy();
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