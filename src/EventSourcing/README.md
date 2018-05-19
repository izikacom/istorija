# How to start with AggregateRoot

Take a look at our tests.

* AggregateRoot : `tests/Eventsourcing/Fixtures/Member.php`
* Entity : `tests/Eventsourcing/Fixtures/Task.php`


## Basic usage

```php
<?php 

use DateTimeImmutable;
use Dayuse\Istorija\EventSourcing\AbstractAggregateRoot;

class Member extends AbstractAggregateRoot
{
    /** @var MemberId */
    private $memberId;

    private function __construct(MemberId $memberId)
    {
        $this->memberId = $memberId;
    }

    public static function register(MemberId $memberId, Username $username, Email $email): self
    {
        $member = new self($memberId);

        $member->recordThat(MemberRegistered::fromArray([
            'memberId' => (string) $memberId,
            'username' => (string) $username,
            'email'    => (string) $email,
            'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));

        return $member;
    }

    public function applyMemberRegistered(MemberRegistered $event): void
    {
        $this->memberId = MemberId::fromString($event->memberId);
    }
    
    public function getId()
    {
        return $this->memberId;
    }
}
```

## Entity management

Let's say :
* **Parent** contains one or more **Entity**
* **Entity** events are managed the same way as any other **EventSourcedObject**
* As **AggregateRoot**, **Entity** have to be identifiable.

### 1. Create & capture an Entity
When your intention is to create an **Entity**, you have to use `EventSourcedObject::capture`

```php
public function createTask(TaskId $taskId)
{
    $this->captureEntity(Task::create($this->memberId, $taskId));
}
```

As you can see, the creation process for a `Task` is encapsulated within the `Task` itself.
The `EventSourcedObject::capture` is used to deal with all recorded events from the **Entity** context.

### 2. Register Entity

From the **Parent** context, you have to register the just created **Entity**.

```php
public function applyTaskCreated(TaskCreated $event)
{
    $task = $this->registerEntity(Task::class, $event);
}
```

The `EventSourcedObject::registerEntity` is used to reconstitute the entity from some **Event**.  
You can store the entity for further purpose.

### 3. Release Entity (destruction, deletion...)

The forward the deletion intention to the concerned **Entity**.  
The **Entity** will record the deletion event.

You will have to deal with the event to release the **Entity**. 

```php
public function deleteTask(TaskId $taskId)
{
    $this->getTaskById($taskId)->delete();
}

public function applyTaskDeleted(TaskDeleted $event)
{
    $this->releaseEntity($event->taskId, $event);
}
```

## Testing

The scenario will help with testing event sourced aggregate roots. A
scenario consists of three steps:

1) given(): Initialize the aggregate root using a history of events
2) when():  A callable that calls one or more methods on the event sourced aggregate root
3) then():  Events that should have been applied

`then()`take an array as argument. Accepted values are :
* ClassName, ie: `TaskCreated::class`
* DomainEvent instance
* Callable: Throw exceptions or `return false` to make a faulty assertion.



```php
$taskId = TaskId::generate();
$userId = UserId::generate();

$scenario = Scenario::monitor(Task::class);
$scenario->given([
    TaskCreated::fromArray([
        'id'   => $taskId,
        'date' => DateTimeImmuatable::now(),
    ]),
]);
$scenario->when(function(Task $task) use($userId) {
    $task->assignTo($userId);
    $task->labelize(['marketing', 'technical'];
    $task->complete();
});
$scenario->then([
    TaskAssigned::fromArray([
        'id'     => $taskId,
        'userId' => $userId,
        'date'   => DateTimeImmutable::now(),
    ]),
    function(TaskLabelized $event) {
        Assertion::count(2, $event->labels);
        
        return false; // will be seen as a faulty assertion.
    },
    TaskCompleted::class,
]);
```

To test factory methods, you could start a Scenario from an instantiated `AggregateRoot`

```php
$taskId   = TaskId::generate();
$task     = Task::create($taskId);
$scenario = Scenario::monitorAndStartFromInstance($task);
```