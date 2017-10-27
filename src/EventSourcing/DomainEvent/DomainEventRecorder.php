<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 29/03/2017
 * Time: 15:59
 */

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

class DomainEventRecorder
{
    /**
     * @var callable
     */
    private $domainEventRecordedCallback;

    /**
     * @var DomainEvent[]
     */
    private $recordedEvents = [];

    /**
     * DomainEventRecorder constructor.
     *
     * @param callable      $domainEventRecordedCallback
     * @param DomainEvent[] $recordedEvents
     */
    public function __construct(callable $domainEventRecordedCallback, array $recordedEvents = array())
    {
        $this->domainEventRecordedCallback = $domainEventRecordedCallback;
        $this->recordedEvents              = $recordedEvents;
    }

    public function recordThat(DomainEvent $event)
    {
        $this->recordedEvents[] = $event;

        call_user_func($this->domainEventRecordedCallback, $event);
    }

    public function recordFromEventRecorder(DomainEventRecorder $other)
    {
        $other->getRecordedEvents()->map(function (DomainEvent $event) {
            $this->recordThat($event);
        });
    }

    /**
     * @return DomainEventCollection
     */
    public function getRecordedEvents()
    {
        return new DomainEventCollection($this->recordedEvents);
    }

    /**
     * @return bool
     */
    public function hasRecordedEvents()
    {
        return !empty($this->recordedEvents);
    }

    /**
     * Clears the recordedEvents
     */
    public function clearRecordedEvents()
    {
        $this->recordedEvents = array();
    }
}