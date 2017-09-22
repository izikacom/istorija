<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;

abstract class AggregateRoot
{
    use EventSourcedObject;

    /**
     * Reconstitute the AggregateRoot state from its event history
     *
     * @param DomainEventCollection $history
     *
     * @return AggregateRoot
     */
    public static function reconstituteFromHistory(DomainEventCollection $history)
    {
        /** @var AggregateRoot $instance */
        $instance = new static();
        $instance->configureEventRecorder();

        foreach ($history as $event) {
            $instance->apply($event);
        }

        return $instance;
    }

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
