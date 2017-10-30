<?php

namespace Dayuse\Istorija\EventStore\Storage;

use Dayuse\Istorija\EventStore\AllEventsReadResult;
use Dayuse\Istorija\EventStore\CommitId;
use Dayuse\Istorija\EventStore\EventData;
use Dayuse\Istorija\EventStore\EventId;
use Dayuse\Istorija\EventStore\EventMetadata;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\EventRecordNotFound;
use Dayuse\Istorija\EventStore\ExpectedVersion;
use Dayuse\Istorija\EventStore\SlicedReadResult;
use Dayuse\Istorija\EventStore\Storage;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\EventStore\UncommittedEvent;
use Dayuse\Istorija\Utils\Ensure;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
class InMemory implements Storage
{
    /**
     * @var array
     */
    private $streamedEvents;

    public function __construct()
    {
        $this->streamedEvents = [];
    }

    public function persist(StreamName $streamName, array $uncommittedEvents, ?int $expectedVersion): void
    {
        Ensure::nullOrLessOrEqualThan($expectedVersion, ExpectedVersion::EMPTY, 'Does not support specific expected version.');

        $commitId = CommitId::generate();
        $events   = array_map(function (UncommittedEvent $uncommittedEvent) use ($streamName, $commitId) {
            return [
                'canonicalStreamName'      => (string)$streamName->getCanonicalStreamName(),
                'streamId'                 => (string)$streamName->getIdentifier(),
                'streamContract'           => (string)$streamName->getContract(),
                'eventId'                  => (string)$uncommittedEvent->getEventId(),
                'eventContract'            => (string)$uncommittedEvent->getContract(),
                'eventData'                => $uncommittedEvent->getData()->getPayload(),
                'eventDataContentType'     => $uncommittedEvent->getData()->getContentType(),
                'eventMetadata'            => $uncommittedEvent->getMetadata()
                    ? $uncommittedEvent->getMetadata()->getPayload()
                    : null,
                'eventMetadataContentType' => $uncommittedEvent->getMetadata()
                    ? $uncommittedEvent->getMetadata()->getContentType()
                    : null,
                'commitId'                 => (string)$commitId,
            ];
        }, $uncommittedEvents);

        $this->streamedEvents[$streamName->getCanonicalStreamName()] = array_merge(
            $this->streamedEvents[$streamName->getCanonicalStreamName()] ?? [],
            $events
        );
    }

    public function delete(StreamName $stream, int $expectedVersion): void
    {
        unset($this->streamedEvents[$stream->getCanonicalStreamName()]);
    }

    public function readEvent(StreamName $streamName, int $eventNumber): EventRecord
    {
        Ensure::keyExists($this->streamedEvents, $streamName->getCanonicalStreamName(), 'Stream not found.');

        $row = $this->streamedEvents[$streamName->getCanonicalStreamName()][$eventNumber] ?? null;

        if (false === $row) {
            throw EventRecordNotFound::onStream($streamName, $eventNumber);
        }

        return $this->eventRecordFromRow($eventNumber, $row);
    }

    public function readStreamEvents(StreamName $streamName, int $start = 0, int $count = PHP_INT_MAX): SlicedReadResult
    {
        Ensure::keyExists($this->streamedEvents, $streamName->getCanonicalStreamName(), 'Stream not found.');

        $events        = $this->streamedEvents[$streamName->getCanonicalStreamName()];
        $partialEvents = array_slice($events, $start, $count);

        if(empty($partialEvents)) {
            return new SlicedReadResult(
                $streamName,
                [],
                $start,
                0
            );
        }

        return new SlicedReadResult(
            $streamName,
            array_map([$this, 'eventRecordFromRow'], range($start, $start + count($partialEvents) - 1), $partialEvents),
            $start,
            count($partialEvents)
        );
    }

    public function readAllEvents(int $start = 0, int $count = PHP_INT_MAX): AllEventsReadResult
    {
        $allEvents = [];

        foreach ($this->streamedEvents as $streamName => $events) {
            $allEvents = array_merge($allEvents, $events);
        }

        if(empty($allEvents)) {
            return new AllEventsReadResult(
                [],
                $start,
                0
            );
        }

        return new AllEventsReadResult(
            array_map([$this, 'eventRecordFromRow'], range($start, $start + count($allEvents) - 1), $allEvents),
            $start,
            count($allEvents)
        );
    }

    protected function eventRecordFromRow(int $eventNumber, array $row): EventRecord
    {
        return new EventRecord(
            EventId::fromString($row['eventId']),
            CommitId::fromString($row['commitId']),
            $eventNumber,
            new EventData($row['eventData'], $row['eventDataContentType']),
            new EventMetadata($row['eventMetadata'], $row['eventMetadataContentType'])
        );
    }

}
