<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventBus\EventBus;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\Exception\NoRecordedEvents;
use DayUse\Istorija\EventStore\EventStore;
use DayUse\Istorija\EventStore\ExpectedVersion;
use DayUse\Istorija\EventStore\StreamName;
use DayUse\Istorija\Identifiers\Identifier;

abstract class EventStoreRepository implements AggregateRootRepository
{
    private $eventStore;
    private $eventEnvelopeFactory;
    private $domainEventFactory;
    private $eventBus;

    public function __construct(EventStore $eventStore, EventEnvelopeFactory $eventEnvelopeFactory,
                                DomainEventFactory $domainEventFactory, EventBus $eventBus)
    {
        $this->eventStore           = $eventStore;
        $this->eventEnvelopeFactory = $eventEnvelopeFactory;
        $this->domainEventFactory   = $domainEventFactory;
        $this->eventBus             = $eventBus;
    }

    abstract protected function streamNameFromIdentifier(Identifier $identifier): StreamName;

    abstract protected function aggregateRootClassFromIdentifier(Identifier $identifier): string;

    public function get(Identifier $aggregateId): AggregateRoot
    {
        // TODO - Load all events from event store about stream Id
        $streamName = $this->streamNameFromIdentifier($aggregateId);
        /** @var AggregateRoot $aggregateClass */
        $aggregateClass = $this->aggregateRootClassFromIdentifier($aggregateId);
        $eventRecords   = $this->getEventStore()->readStreamEventsForward($streamName);
        $domainEvents   = $this->getDomainEventFactory()->fromEventRecords($eventRecords);

        return $aggregateClass::reconstituteFromHistory($domainEvents);
    }

    public function save(AggregateRoot $aggregateRoot): void
    {
        if (!$aggregateRoot->hasRecordedEvents()) {
            throw new NoRecordedEvents();
        }

        $streamName     = $this->streamNameFromIdentifier($aggregateRoot->getId());
        $domainEvents   = $aggregateRoot->getRecordedEvents();
        $eventEnvelopes = $this->getEventEnvelopeFactory()->fromDomainEvents($domainEvents);

        $this->getEventStore()->append(
            $streamName,
            ExpectedVersion::ANY,
            $eventEnvelopes
        );

        $aggregateRoot->clearRecordedEvents();

        $this->getEventBus()->publishAll($domainEvents->map(function (DomainEvent $event) {
            return $event;
        }));
    }

    protected function getEventStore(): EventStore
    {
        return $this->eventStore;
    }

    protected function getEventEnvelopeFactory(): EventEnvelopeFactory
    {
        return $this->eventEnvelopeFactory;
    }

    protected function getDomainEventFactory(): DomainEventFactory
    {
        return $this->domainEventFactory;
    }

    protected function getEventBus(): EventBus
    {
        return $this->eventBus;
    }
}
