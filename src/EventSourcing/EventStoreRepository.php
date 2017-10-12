<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventBus\EventBus;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use DayUse\Istorija\EventStore\EventStore;
use DayUse\Istorija\EventStore\ExpectedVersion;
use DayUse\Istorija\EventStore\StreamName;
use DayUse\Istorija\Identifiers\Identifier;

abstract class EventStoreRepository implements AggregateRootRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventEnvelopeFactory
     */
    private $eventEnvelopeFactory;

    /**
     * @var DomainEventFactory
     */
    private $domainEventFactory;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * EventStoreRepository constructor.
     *
     * @param EventStore           $eventStore
     * @param EventEnvelopeFactory $eventEnvelopeFactory
     * @param DomainEventFactory   $domainEventFactory
     * @param EventBus             $eventBus
     */
    public function __construct(EventStore $eventStore, EventEnvelopeFactory $eventEnvelopeFactory, DomainEventFactory $domainEventFactory, EventBus $eventBus)
    {
        $this->eventStore           = $eventStore;
        $this->eventEnvelopeFactory = $eventEnvelopeFactory;
        $this->domainEventFactory   = $domainEventFactory;
        $this->eventBus             = $eventBus;
    }

    abstract protected function streamNameFromIdentifier(Identifier $identifier): StreamName;

    abstract protected function aggregateRootClassFromIdentifier(Identifier $identifier): string;

    public function get(Identifier $aggregateId)
    {
        // TODO - Load all events from event store about stream Id
        $streamName     = $this->streamNameFromIdentifier($aggregateId);
        $aggregateClass = $this->aggregateRootClassFromIdentifier($aggregateId);
        $eventRecords   = $this->eventStore->readStreamEventsForward($streamName);
        $domainEvents   = $this->domainEventFactory->fromEventRecords($eventRecords);

        return $aggregateClass::reconstituteFromHistory($domainEvents);
    }

    public function save(AggregateRoot $aggregateRoot)
    {
        if (!$aggregateRoot->hasRecordedEvents()) {
            return;
        }

        $streamName     = $this->streamNameFromIdentifier($aggregateRoot->getId());
        $domainEvents   = $aggregateRoot->getRecordedEvents();
        $eventEnvelopes = $this->eventEnvelopeFactory->fromDomainEvents($domainEvents);

        $this->eventStore->append(
            $streamName,
            ExpectedVersion::ANY,
            $eventEnvelopes
        );

        $aggregateRoot->clearRecordedEvents();

        $this->eventBus->publishAll($domainEvents->map(function (DomainEvent $event) {
            return $event;
        }));
    }
}
