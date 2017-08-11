<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../vendor/autoload.php';

use Bgy\Istorija\Utils\Contract;
use Bgy\Istorija\EventStore\EventData;
use Bgy\Istorija\EventStore\EventRecord;
use Bgy\Istorija\EventStore\EventMetadata;
use Bgy\Istorija\EventStore\EventStore;
use Bgy\Istorija\EventStore\EventEnvelope;
use Bgy\Istorija\EventStore\ExpectedVersion;
use Bgy\Istorija\EventStore\StreamName;
use Bgy\Istorija\Identifiers\GenericUuidIdentifier;

$configuration = \Bgy\Istorija\EventStore\ConfigurationBuilder::create()
    ->usingMysqlDbalStorage('mysql://root@192.168.33.10/event_store')
    ->initializeStorageIfRequired()
    ->build()
;

$es = new EventStore($configuration);

// Generate a bunch of raw events
$events = [];
for ($i = 0; $i <= 50; ++$i) {
    $events[] = EventEnvelope::wrap(
        Contract::with('OrderPlaced'),
        new EventData(json_encode(['id' => $i, 'username' => sprintf('Boris-%d', $i)]), 'application/json'),
        new EventMetadata(json_encode(['actor' => 'System Administrator']), 'application/json')
    );
}

// Append them in a single transaction
$toStream = new StreamName(GenericUuidIdentifier::fromString('5df9a4e4-0999-11e7-bdb3-024b86486f93'), Contract::with('Order'));
$es->append($toStream, ExpectedVersion::ANY, $events);

// Read them all back from the ES
$events = $es->readAllEvents();

/** @var EventRecord[] $events */
foreach ($events as $eventRecord) {
    printf("Stream{%s}: %s\n", $toStream, $eventRecord->getData()->getPayload());
}


$es->delete($toStream, $expectedVersion = $i);
