# EventStore

## Storage

### Configure the EventStore using a MySQL Storage

#### Create MySQL database
```SQL
CREATE DATABASE `istorija_event_store` DEFAULT CHARACTER SET = `utf8mb4`;
```

#### Use the Configuration Builder to set up your Event Store

```php
$configuration = \Bgy\Istorija\EventStore\ConfigurationBuilder::create()
    ->usingMysqlDbalStorage('mysql://root@192.168.33.10/istorija_event_store')
    ->initializeStorageIfRequired() // Will create the required table or any required initialization for your storage
    ->build()
;

$eventStore = new EventStore($configuration);
```

### Append events

```php
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
$eventStore->append($toStream, ExpectedVersion::ANY, $events);
```

### Reading events

```php
// Read them all back from the ES
$events = $eventStore->readAllEvents();

/** @var EventRecord[] $events */
foreach ($events as $eventRecord) {
    printf("Stream{%s}: %s\n", $toStream, $eventRecord->getData()->getPayload());
}
```

### Deleting a stream

```php
$eventStore->delete($toStream, ExpectedVersion::ANY);
```
