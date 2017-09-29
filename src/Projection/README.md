# How to use a Query

```php
<?php

use \DayUse\Istorija\EventStore\EventMetadata;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\Query;
use \DayUse\Istorija\Projection\Player\SimplePlayer;

$query = (new Query())
    ->init(function() {
        return 0;
    })
    ->when([
        'UserCreated' => function(DomainEvent $event, $previous, EventMetadata $metadata) {
            return $previous + 1;
        },
    ]);

$player = new SimplePlayer(
    new DomainEventFactory(), 
    $eventStore, 
    $query
);
$player->playFromBeginning();

$numUserCreation = $query->getState();
?>
```

# How to use a Projector

```php
<?php

use \DayUse\Istorija\EventStore\EventMetadata;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\Projector;
use \DayUse\Istorija\Projection\Player\SimplePlayer;

$projector = new class() extends Projector {
    public function initialState() {
        return 0;
    }
    
    public function whenUserCreated(DomainEvent $event, $previous, EventMetadata $metadata) {
        return $previous + 1;
    }
};

$player = new SimplePlayer(
    new DomainEventFactory(), 
    $eventStore, 
    $projector
);
$player->playFromBeginning();

$numUserCreation = $projector->getState();
?>
```

# How to use a PersistedProjector

The state of this projector is stored using a DAO.
For the example, I'm using a Buffered DAO.

```php
<?php

use \DayUse\Istorija\Utils\Ensure;
use \DayUse\Istorija\EventStore\EventMetadata;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\PersistedProjector;
use \DayUse\Istorija\Projection\Player\SimplePlayer;
use \DayUse\Istorija\DAO\Storage\InMemoryDAO;
use \DayUse\Istorija\DAO\Proxy\Buffer;
use \DayUse\Istorija\DAO\DAOInterface;

$dao = new Buffer(new InMemoryDAO(), new InMemoryDAO());
$projector = new class($dao) extends PersistedProjector {
    private $dao;
    
    public function __construct(DAOInterface $dao) {
        $this->dao = $dao;
    }
    
    public function initialState() {
        return 0;
    }
    
    protected function getName(): string {
        return 'users';
    }
    
    protected function getDAO(): DAOInterface {
        return $this->dao;
    }
    
    public function whenUserCreated(DomainEvent $event, $previous, EventMetadata $metadata) {
        return $previous + 1;
    }
};

$player = new SimplePlayer(
    new DomainEventFactory(), 
    $eventStore, 
    $projector
);
$player->playFromBeginning();

$dao->flushAndCommit();

$numUserCreation = $projector->getState();
Ensure::eq($projector->getState(), $dao->find('users'));
?>
```

# How to use a PersistedPartitionedProjector

Used to build complex state; like a list of members.

```php
<?php

use \DayUse\Istorija\Utils\Ensure;
use \DayUse\Istorija\EventStore\EventMetadata;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\PersistedPartitionedProjector;
use \DayUse\Istorija\Projection\Player\SimplePlayer;
use \DayUse\Istorija\DAO\Storage\InMemoryDAO;
use \DayUse\Istorija\DAO\DAOInterface;

$dao = new InMemoryDAO();
$projector = new class($dao) extends PersistedPartitionedProjector {
    private $dao;
    
    public function __construct(DAOInterface $dao) {
        $this->dao = $dao;
    }
    
    protected function findPartitionFromEvent(DomainEvent $event, EventMetadata $metadata) {
        switch(get_class($event)) {
            case "UserCreated":
            case "UserEmailConfirmed":
                return $event->userId;
            default:
                throw new \InvalidArgumentException(sprintf('Are you sure that this %s event can be handled?', get_class($event)));
        }
    }
    
    protected function getDAO(): DAOInterface {
        return $this->dao;
    }
    
    public function whenUserCreated(DomainEvent $event, $previous, EventMetadata $metadata) {
        return [
            'userId'    => '123',
            'confirmed' => false,
        ];
    }
};

$player = new SimplePlayer(
    new DomainEventFactory(), 
    $eventStore, 
    $projector
);
$player->playFromBeginning();

Ensure::eq([
    'userId'    => '123',
    'confirmed' => false,
],$dao->find('123'));
?>
```