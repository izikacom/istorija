<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

use DayUse\Istorija\Utils\Ensure;

class EventRecord
{
    private $eventId;
    private $eventNumber;
    private $commitId;
    private $data;
    private $metadata;

    public function __construct(EventId $id, CommitId $commitId, int $eventNumber,
        EventData $data, ?EventMetadata $metadata)
    {
        Ensure::min($eventNumber, 0);
        $this->eventId = $id;
        $this->commitId = $commitId;
        $this->eventNumber = $eventNumber;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    public function getEventId(): EventId
    {
        return $this->eventId;
    }

    public function getEventNumber(): int
    {
        return $this->eventNumber;
    }

    public function getCommitId(): CommitId
    {
        return $this->commitId;
    }

    public function getData(): EventData
    {
        return $this->data;
    }

    public function getMetadata(): ?EventMetadata
    {
        return $this->metadata;
    }
}
