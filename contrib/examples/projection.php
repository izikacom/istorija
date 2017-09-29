<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../../vendor/autoload.php';

use DayUse\Istorija\Utils\Contract;
use DayUse\Istorija\EventStore\EventData;
use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\EventStore\EventMetadata;
use DayUse\Istorija\EventStore\EventStore;
use DayUse\Istorija\EventStore\EventEnvelope;
use DayUse\Istorija\EventStore\ExpectedVersion;
use DayUse\Istorija\EventStore\StreamName;
use DayUse\Istorija\Identifiers\GenericUuidIdentifier;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\Query;
use \DayUse\Istorija\Projection\Player\SimplePlayer;

$configuration = \DayUse\Istorija\EventStore\ConfigurationBuilder::create()
    ->usingMySqlDbalStorage('mysql://root@192.168.33.10/event_store')
    ->initializeStorageIfRequired()
    ->build()
;

$es = new EventStore($configuration);

$placeOrder = function($num) use($es) {
    // Generate a bunch of raw events
    $events = [];
    for ($i = 0; $i <= $num; ++$i) {
        $events[] = EventEnvelope::wrap(
            Contract::with('OrderPlaced'),
            new EventData(json_encode(['id' => $i, 'username' => sprintf('Boris-%d', $i)]), 'application/json'),
            new EventMetadata(json_encode(['actor' => 'System Administrator']), 'application/json')
        );
    }

    // Append them in a single transaction
    $toStream = new StreamName(GenericUuidIdentifier::fromString('5df9a4e4-0999-11e7-bdb3-024b86486f93'), Contract::with('Order'));
    $es->append($toStream, ExpectedVersion::ANY, $events);
};


// projection part
$placeOrder(10);

$query = (new Query())
    ->init(function() {
        return 0;
    })
    ->when([
        'OrderPlaced' => function(DomainEvent $event, $previous, EventMetadata $metadata) {
            return $previous + 1;
        },
    ]);

$player = new SimplePlayer(
    new DomainEventFactory(),
    $es,
    $query
);
$player->playFromBeginning();

$numOrderPlaced = $query->getState(); // value: 10