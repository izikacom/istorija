<?php

namespace DayUse\Test\Istorija\EventSourcing\Fixtures;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

class TaskCreated implements DomainEvent
{
    public $memberId;
    public $taskId;
    public $completed;
    public $date;

    /**
     * @param array $data
     *
     * @return TaskCreated
     */
    public static function fromArray(array $data)
    {
        $event            = new TaskCreated();
        $event->memberId  = $data['memberId'];
        $event->taskId    = $data['taskId'];
        $event->completed = $data['completed'];
        $event->date      = $data['date'];

        return $event;
    }
}
