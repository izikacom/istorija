# How to use a StatefulProjector

WIP SECTION !!!

The state of this projector is stored using a DAO.
For the example, I'm using a Buffered DAO.

```php
<?php

use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use \Dayuse\Istorija\Projection\Projector;
use \Dayuse\Istorija\Projection\Player\SimplePlayer;
use \Dayuse\Istorija\Serializer\JsonObjectSerializer;
use \Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use \Dayuse\Istorija\DAO\Proxy\Buffer;
use \Dayuse\Istorija\DAO\DAOInterface;

$dao = new Buffer(new InMemoryDAO(), new InMemoryDAO());
$projector = new class($dao) extends StatefulProjector {
    public function whenUserCreated(DomainEvent $event) {
        $this->setState($event->getUser(), function(State $state) use ($event) {
            return $state->set('created', true);
        });
    }
};

$player = new SimplePlayer(
    $eventStore, 
    new EventStoreMessageTranslator(new JsonObjectSerializer()), 
    $projector
);
$player->playFromBeginning();

$dao->flushAndCommit();

$users = $dao->find('users');

?>
```