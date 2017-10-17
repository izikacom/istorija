<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 26/09/2017
 * Time: 09:36
 */

namespace DayUse\Istorija\Projection\Player;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use DayUse\Istorija\EventSourcing\EventStoreMessageTranslator;
use DayUse\Istorija\EventStore\EventStore;
use DayUse\Istorija\Projection\Projection;

class SimplePlayer
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventStoreMessageTranslator
     */
    private $eventStoreMessageTranslator;

    /**
     * @var Projection
     */
    private $projection;

    /**
     * SimplePlayer constructor.
     *
     * @param EventStore                  $eventStore
     * @param EventStoreMessageTranslator $eventStoreMessageTranslator
     * @param Projection                  $projection
     */
    public function __construct(EventStore $eventStore, EventStoreMessageTranslator $eventStoreMessageTranslator, Projection $projection)
    {
        $this->eventStore                  = $eventStore;
        $this->eventStoreMessageTranslator = $eventStoreMessageTranslator;
        $this->projection                  = $projection;
    }

    public function playFromBeginning()
    {
        $eventRecords = $this->eventStore->readAllEvents();

        $this->projection->reset();

        foreach($eventRecords as $eventRecord) {
            $domainEvent = $this->eventStoreMessageTranslator->fromEventRecord($eventRecord);

            $this->projection->apply($domainEvent);
        }
    }

    public function subscribeToLive()
    {

    }

    /**
     * Call $onEvent on dispatched event; can throttle to avoid spamming.
     *
     * @param callable $onEvent
     * @param int      $updateThrottled
     */
    public function playFromBeginningThenSwitchToLiveSubscription(callable $onEvent, $updateThrottled = 0)
    {
        // 1. subscribe to event-sourcing & store in-memory all dispatched event
        // 2. store last checkpoint number
        // 3. replay event-store from 0 to last stored checkpoint number
        // 4. apply stored event from subscription; when empty, switch to live subscription
    }
}