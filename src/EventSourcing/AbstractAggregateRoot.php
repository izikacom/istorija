<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;

abstract class AbstractAggregateRoot
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
