# How to use a Query

```php
<?php

use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use \Dayuse\Istorija\Projection\Query;
use \Dayuse\Istorija\Projection\Player\SimplePlayer;
use \Dayuse\Istorija\Serializer\JsonObjectSerializer;

$query = (new Query())
    ->init(function() {
        return 0;
    })
    ->when([
        'UserCreated' => function($state, DomainEvent $event) {
            return $state + 1;
        },
    ]);

$player = new SimplePlayer(
    $eventStore, 
    new EventStoreMessageTranslator(new JsonObjectSerializer()),
    $query
);
$player->playFromBeginning();

$numUserCreation = $query->getState();
?>
```

# How to use a Projector

```php
<?php

use \Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \Dayuse\Istorija\EventSourcing\EventStoreMessageTranslator;
use \Dayuse\Istorija\Projection\Projector;
use \Dayuse\Istorija\Projection\Player\SimplePlayer;
use \Dayuse\Istorija\Serializer\JsonObjectSerializer;

$projector = new class() extends Projector {
    public $state;
    
    public function init(): void {
        $this->state = 0;
    }
    
    public function reset(): void {
        $this->state = 0;
    }
    
    public function whenUserCreated(DomainEvent $event) {
        $this->state++;
    }
};

$player = new SimplePlayer(
    $eventStore, 
    new EventStoreMessageTranslator(new JsonObjectSerializer()),
    $projector
);
$player->playFromBeginning();

$numUserCreation = $projector->state;
?>
```

# How to use a DAO with Projector

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
$projector = new class($dao) extends Projector {
    private $dao;
    
    public function __construct(DAOInterface $dao) {
        $this->dao = $dao;
    }
    
    public function init(): void {
        $this->dao->save('users', 0);
    }
    
    public function reset(): void {
        $this->dao->flush();
    }
    
    public function whenUserCreated(DomainEvent $event) {
        $this->dao->save('users', $this->dao->find('users') + 1);
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
