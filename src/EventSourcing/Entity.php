<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventRecorder;

abstract class Entity
{
    use EventSourcedObject;

    public static function reconstituteFromSingleEvent(DomainEvent $event)
    {
        /** @var AggregateRoot $instance */
        $instance = new static();
        $instance->configureEventRecorder();
        $instance->apply($event);

        return $instance;
    }

    /**
     * 1. Copy recorded event
     * 2. Switch to the other event recorder
     *
     * @param DomainEventRecorder $otherEventRecorder
     * @param bool          $doCopy
     */
    public function changeEventRecorder(DomainEventRecorder $otherEventRecorder, bool $doCopy)
    {
        if($doCopy) {
            $otherEventRecorder->recordFromEventRecorder($this->domainEventRecorder);
        }

        $this->domainEventRecorder = $otherEventRecorder;
    }
}
