<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

require __DIR__ . '/../../vendor/autoload.php';

use Dayuse\Istorija\Utils\Contract;
use Dayuse\Istorija\EventStore\EventData;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\EventMetadata;
use Dayuse\Istorija\EventStore\EventStore;
use Dayuse\Istorija\EventStore\EventEnvelope;
use Dayuse\Istorija\EventStore\ExpectedVersion;
use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Identifiers\GenericUuidIdentifier;
use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \Dayuse\Istorija\Projection\Query;
use \Dayuse\Istorija\Projection\Player\SimplePlayer;

$configuration = \Dayuse\Istorija\EventStore\ConfigurationBuilder::create()
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
        'OrderPlaced' => function($state, DomainEvent $event, EventMetadata $metadata) {
            return $state + 1;
        },
    ]);

$player = new SimplePlayer(
    new DomainEventFactory(),
    $es,
    $query
);
$player->playFromBeginning();

$numOrderPlaced = $query->getState(); // value: 10