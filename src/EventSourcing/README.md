# How to start with AggregateRoot

Take a look at our tests.

* AggregateRoot : `tests/Eventsourcing/Fixtures/Member.php`
* Entity : `tests/Eventsourcing/Fixtures/Task.php`

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