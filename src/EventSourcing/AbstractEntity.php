<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventRecorder;

abstract class AbstractEntity
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
