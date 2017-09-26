<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 26/09/2017
 * Time: 09:36
 */

namespace DayUse\Istorija\Projection\Player;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use DayUse\Istorija\EventStore\EventStore;
use DayUse\Istorija\Projection\EventHandler;

class SimplePlayer
{
    /**
     * @var DomainEventFactory
     */
    private $domainEventFactory;

    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventHandler
     */
    private $eventHandler;

    /**
     * SimplePlayer constructor.
     *
     * @param DomainEventFactory $domainEventFactory
     * @param EventStore         $eventStore
     * @param EventHandler       $eventHandler
     */
    public function __construct(DomainEventFactory $domainEventFactory, EventStore $eventStore, EventHandler $eventHandler)
    {
        $this->domainEventFactory = $domainEventFactory;
        $this->eventStore         = $eventStore;
        $this->eventHandler       = $eventHandler;
    }

    public function playFromScratch()
    {
        $eventRecords = $this->eventStore->readAllEvents();

        foreach($eventRecords as $eventRecord) {
            $this->eventHandler->apply(
                $this->domainEventFactory->fromEventRecord($eventRecord),
                $eventRecord->getMetadata()
            );
        }
    }
}