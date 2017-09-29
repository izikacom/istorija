<?php

namespace DayUse\Test\Istorija\EventSourcing\Fixtures;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

class TaskCompleted implements DomainEvent
{
    public $memberId;
    public $taskId;
    public $date;

    /**
     * @param array $data
     *
     * @return TaskCompleted
     */
    public static function fromArray(array $data)
    {
        $event            = new TaskCompleted();
        $event->memberId  = $data['memberId'];
        $event->taskId    = $data['taskId'];
        $event->date      = $data['date'];

        return $event;
    }
}
