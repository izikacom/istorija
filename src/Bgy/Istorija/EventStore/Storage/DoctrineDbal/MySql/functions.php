<?php

namespace Bgy\Istorija\EventStore\Storage\DoctrineDbal\Mysql;

use Bgy\Istorija\EventStore\CommitId;
use Bgy\Istorija\EventStore\EventData;
use Bgy\Istorija\EventStore\EventId;
use Bgy\Istorija\EventStore\EventMetadata;
use Bgy\Istorija\EventStore\EventRecord;

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
