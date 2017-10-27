<?php

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql;

use Dayuse\Istorija\EventStore\CommitId;
use Dayuse\Istorija\EventStore\EventData;
use Dayuse\Istorija\EventStore\EventId;
use Dayuse\Istorija\EventStore\EventMetadata;
use Dayuse\Istorija\EventStore\EventRecord;

function hydrateFromRow(array $row, int $eventNumber)
{
    return new EventRecord(
        EventId::fromString($row['eventId']),
        CommitId::fromString($row['commitId']),
        $eventNumber,
        new EventData($row['eventData'], $row['eventDataContentType']),
        new EventMetadata($row['eventMetadata'], $row['eventMetadataContentType'])
    );
}
