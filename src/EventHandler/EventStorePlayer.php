<?php

namespace DayUse\Istorija\EventHandler;

use Dayuse\Istorija\EventSourcing\EventHandler;
use Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use Dayuse\Istorija\EventStore\EventStore;


/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class EventStorePlayer
{
    /** @var EventStore */
    private $eventStore;

    /** @var EventStoreMessageTranslator */
    private $eventStoreMessageTranslator;

    /** @var EventHandler */
    private $eventHandler;

    /**
     * SimplePlayer constructor.
     *
     * @param EventStore                  $eventStore
     * @param EventStoreMessageTranslator $eventStoreMessageTranslator
     * @param EventHandler                $eventHandler
     */
    public function __construct(
        EventStore $eventStore,
        EventStoreMessageTranslator $eventStoreMessageTranslator,
        eventHandler $eventHandler
    ) {
        $this->eventStore                  = $eventStore;
        $this->eventStoreMessageTranslator = $eventStoreMessageTranslator;
        $this->eventHandler                = $eventHandler;
    }

    public function playFromBeginning(): void
    {
        $eventRecords = $this->eventStore->readAllEvents();

        foreach ($eventRecords as $eventRecord) {
            $domainEvent = $this->eventStoreMessageTranslator->fromEventRecord($eventRecord);

            $this->eventHandler->apply($domainEvent);
        }
    }

    public function subscribeToLive(): void
    {
    }

    /**
     * Call $onEvent on dispatched event; can throttle to avoid spamming.
     *
     * @param callable $onEvent
     * @param int      $updateThrottled
     */
    public function playFromBeginningThenSwitchToLiveSubscription(callable $onEvent, $updateThrottled = 0): void
    {
        // 1. subscribe to event-sourcing & store in-memory all dispatched event
        // 2. store last checkpoint number
        // 3. replay event-store from 0 to last stored checkpoint number
        // 4. apply stored event from subscription; when empty, switch to live subscription
    }
}