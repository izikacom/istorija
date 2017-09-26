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
use DayUse\Istorija\Projection\Projection;

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
     * @var Projection
     */
    private $projection;

    /**
     * SimplePlayer constructor.
     *
     * @param DomainEventFactory $domainEventFactory
     * @param EventStore         $eventStore
     * @param Projection         $projection
     */
    public function __construct(DomainEventFactory $domainEventFactory, EventStore $eventStore, Projection $projection)
    {
        $this->domainEventFactory = $domainEventFactory;
        $this->eventStore         = $eventStore;
        $this->projection         = $projection;
    }

    public function playFromBeginning()
    {
        $eventRecords = $this->eventStore->readAllEvents();

        $this->projection->reset();

        foreach($eventRecords as $eventRecord) {
            $this->projection->apply(
                $this->domainEventFactory->fromEventRecord($eventRecord),
                $eventRecord->getMetadata()
            );
        }
    }
}