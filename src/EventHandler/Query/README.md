# How to use a Query

```php
<?php

use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use \Dayuse\Istorija\EventHandler\Query\QueryBuilder;
use \Dayuse\Istorija\EventHandler\State;
use \Dayuse\Istorija\EventHandler\EventStorePlayer;
use \Dayuse\Istorija\Serializer\JsonObjectSerializer;
use \Dayuse\Istorija\EventStore\EventStore;
use \Dayuse\Istorija\EventStore\Configuration;
use \Dayuse\Istorija\EventStore\Storage\InMemory as InMemoryEventStorage;

$query = (new QueryBuilder())
    ->init(function() {
        return new State([
            'numUsers' => 0,
        ]);
    })
    ->when([
        'UserCreated' => function(State $previousState, DomainEvent $event) {
            return $previousState->set('numUsers', $previousState->get('numUsers') + 1);
        },
    ])->getQuery();

$player = new EventStorePlayer(
    new EventStore(
        new Configuration(new InMemoryEventStorage())        
    ),
    new EventStoreMessageTranslator(new JsonObjectSerializer()),
    $query
);
$player->playFromBeginning();

$numUserCreation = $query->getState()->get('numUsers');
?>
```
