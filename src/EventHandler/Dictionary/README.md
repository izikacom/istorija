# Why a Dictionary ?

Used to store a simple projection within a persisted storage.

# How to use a Dictionary

```php
<?php

use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use \Dayuse\Istorija\EventHandler\Dictionary\AbstractDictionaryGenerator;
use \Dayuse\Istorija\EventHandler\State;
use \Dayuse\Istorija\EventHandler\EventStorePlayer;
use \Dayuse\Istorija\EventHandler\Dictionary\Dictionary;
use \Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use \Dayuse\Istorija\Serializer\JsonObjectSerializer;
use \Dayuse\Istorija\EventStore\EventStore;
use \Dayuse\Istorija\EventStore\Configuration;
use \Dayuse\Istorija\EventStore\Storage\InMemory as InMemoryEventStorage;

$dictionary = new Dictionary(new InMemoryDAO());
$memberGenerator = new class($dictionary) extends AbstractDictionaryGenerator {
    public static function getInitialState(): State {
        return State::createEmpty();
    }
    
    public function whenMemberCreated(DomainEvent $event): void {
        $this->setState('member-123', function(State $previous) {
            return $previous
                ->set('name', 'John Doe')
                ->set('email', 'john.doe@acme.com');
        });
    }
};

$player = new EventStorePlayer(
    new EventStore(
        new Configuration(new InMemoryEventStorage())        
    ),
    new EventStoreMessageTranslator(new JsonObjectSerializer()),
    $memberGenerator
);
$player->playFromBeginning();

$dictionary->get('member-123');

?>
```