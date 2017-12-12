<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventBus\EventBus;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\Exception\NoRecordedEvents;
use Dayuse\Istorija\EventStore\EventStore;
use Dayuse\Istorija\EventStore\ExpectedVersion;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Identifiers\Identifier;

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

    public function get(Identifier $aggregateId): AbstractAggregateRoot
    {
        // TODO - Load all events from event store about stream Id
        $streamName = $this->streamNameFromIdentifier($aggregateId);
        /** @var AbstractAggregateRoot $aggregateClass */
        $aggregateClass = $this->aggregateRootClassFromIdentifier($aggregateId);
        $eventRecords   = $this->getEventStore()->readStreamEventsForward($streamName);
        $domainEvents   = $this->getDomainEventFactory()->fromEventRecords($eventRecords);

        return $aggregateClass::reconstituteFromHistory($domainEvents);
    }

    public function save(AbstractAggregateRoot $aggregateRoot): void
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
