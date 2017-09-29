<?php

namespace DayUse\Test\Istorija\EventSourcing\Fixtures;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

class MemberConfirmedEmail implements DomainEvent
{
    public $memberId;

    /**
     * @param array $data
     * @return MemberConfirmedEmail
     */
    public static function fromArray(array $data)
    {
        $event = new MemberConfirmedEmail();
        $event->memberId = $data['memberId'];

        return $event;
    }
}
