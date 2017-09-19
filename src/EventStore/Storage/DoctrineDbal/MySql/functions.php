<?php

namespace DayUse\Istorija\EventStore\Storage\DoctrineDbal\MySql;

use DayUse\Istorija\EventStore\CommitId;
use DayUse\Istorija\EventStore\EventData;
use DayUse\Istorija\EventStore\EventId;
use DayUse\Istorija\EventStore\EventMetadata;
use DayUse\Istorija\EventStore\EventRecord;

function hydrateFromRow(array $row, int $eventNumber)
{
    return new EventRecord(
        EventId::fromString($row['eventId']),
        CommitId::fromString($row['commitId']),
        $eventNumber,
        new EventData($row['eventData'], $row['eventDataContentType']),
        new EventMetadata($row['eventData'], $row['eventDataContentType'])
    );
}
