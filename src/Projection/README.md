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
$player->playFromScratch();

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
    public function initialization() {
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
$player->playFromScratch();

$numUserCreation = $projector->getState();
?>
```

# How to use a ReadModelProjector - replay case

```php
<?php

use \DayUse\Istorija\Utils\Ensure;
use \DayUse\Istorija\EventStore\EventMetadata;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use \DayUse\Istorija\EventSourcing\DomainEvent\DomainEventFactory;
use \DayUse\Istorija\Projection\ReadModelProjector;
use \DayUse\Istorija\Projection\Player\SimplePlayer;
use \DayUse\Istorija\ReadModel\Storage\InMemoryDAO;
use \DayUse\Istorija\ReadModel\DAOInterface;

$readModel = new InMemoryDAO();
$projector = new class($readModel) extends ReadModelProjector {
    private $readModel;
    
    public function __construct(DAOInterface $readModel) {
        $this->readModel = $readModel;
    }
    
    public function initialization() {
        $this->getReadModel()->save('users', 0);
        
        return 0;
    }
    
    public function getReadModel(): DAOInterface {
        return $this->readModel;
    }
    
    public function whenUserCreated(DomainEvent $event, $previous, EventMetadata $metadata) {
        $current = $previous + 1;
        
        $this->getReadModel()->save('users', $current);
        
        return $current;
    }
};

$player = new SimplePlayer(
    new DomainEventFactory(), 
    $eventStore, 
    $projector
);
$player->playFromScratch();

$numUserCreation = $projector->getState();

Ensure::eq($projector->getState(), $readModel->find('users'));
?>
```