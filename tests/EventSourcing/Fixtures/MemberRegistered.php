<?php

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;

class MemberRegistered implements DomainEvent
{
    public $memberId;
    public $username;
    public $email;
    public $date;

    /**
     * @param array $data
     * @return MemberRegistered
     */
    public static function fromArray(array $data)
    {
        $event = new MemberRegistered();
        $event->memberId = $data['memberId'];
        $event->username = $data['username'];
        $event->email = $data['email'];
        $event->date = $data['date'];

        return $event;
    }
}
