<?php

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\EventStore\Storage\RequiresInitialization;
use Bgy\Istorija\Utils\Ensure;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
final class EventStore
{
    private $storage;

    public function __construct(Configuration $configuration)
    {
        $this->storage = $configuration->getStorage();
        if ($this->storage instanceof RequiresInitialization && $configuration->shouldInitializeStorage()) {
            $this->storage->initialize();
        }
    }

    public function append(StreamName $stream, ?int $expectedVersion, array $events): void
    {
        Ensure::greaterOrEqualThan($expectedVersion, -1);
        Ensure::allIsInstanceOf($events, EventEnvelope::class);
        $expectedVersion = $expectedVersion ?? ExpectedVersion::ANY;

        $uncommittedEvents = [];
        foreach ($events as $event) {
            $uncommittedEvents[] = new UncommittedEvent(
                EventId::generate(),
                $event->getContract(),
                $event->getEventData(),
                $event->getEventMetadata()
            );
        }

        $this->storage->persist($stream, $uncommittedEvents, $expectedVersion);
    }

    public function delete(StreamName $stream, ?int $expectedVersion): void
    {
        Ensure::min($expectedVersion, -1);
        $expectedVersion = $expectedVersion ?? ExpectedVersion::ANY;

        $this->storage->delete($stream, $expectedVersion);
    }

    public function readEventFrom(StreamName $stream, int $eventNumber): EventRecord
    {
        Ensure::notEmpty($stream);
        Ensure::min($eventNumber, 1);

        return $this->storage->readEvent($stream, $eventNumber);
    }

    public function readStreamEventsForward(StreamName $stream, ?int $start = 0, ?int $count = PHP_INT_MAX): SlicedReadResult
    {
        Ensure::nullOrMin($start, 0);
        Ensure::nullOrMin($count, 0);

        return $this->storage->readStreamEvents($stream, $start, $count);
    }

    public function readAllEvents(?int $start = 0, ?int $count = PHP_INT_MAX): AllEventsReadResult
    {
        Ensure::nullOrMin($start, 0);
        Ensure::nullOrMin($count, 0);

        return $this->storage->readAllEvents($start, $count);
    }

    public function readUsingAdvancedQuery(AdvancedReadQuery $query)
    {
        if (!$this->storage instanceof AdvancedStorage) {

            throw AdvancedStorageNotAvailable::onStorage($this->storage);
        }

        if (!$this->storage->supportsAdvancedReadQuery($query)) {
            throw AdvancedStorageNotAvailable::forReadQuery($this->storage, $query);
        }

        return $this->storage->readUsingAdvancedQuery($query);
    }
}
