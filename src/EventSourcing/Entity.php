<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventRecorder;

abstract class Entity
{
    use EventSourcedObject;

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
