<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

abstract class AggregateRoot
{
    use EventSourcedObject;

    public function getRecordedEvents()
    {
        return $this->domainEventRecorder->getRecordedEvents();
    }

    public function hasRecordedEvents()
    {
        return $this->domainEventRecorder->hasRecordedEvents();
    }

    public function clearRecordedEvents()
    {
        $this->domainEventRecorder->clearRecordedEvents();
    }

    public function isEventCanBeApplied(DomainEvent $event)
    {
        return true;
    }
}
