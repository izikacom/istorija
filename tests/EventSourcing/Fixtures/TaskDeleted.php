<?php

namespace DayUse\Test\Istorija\EventSourcing\Fixtures;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

class TaskDeleted implements DomainEvent
{
    public $memberId;
    public $taskId;
    public $date;

    /**
     * @param array $data
     *
     * @return TaskDeleted
     */
    public static function fromArray(array $data)
    {
        $event            = new TaskDeleted();
        $event->memberId  = $data['memberId'];
        $event->taskId    = $data['taskId'];
        $event->date      = $data['date'];

        return $event;
    }
}
