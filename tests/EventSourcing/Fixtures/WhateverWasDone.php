<?php

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use CosplayIt\ESD\EventSourcing\DomainEvent\DomainEvent;

class WhateverWasDone implements DomainEvent
{
    public $foo;
    public $bar;

    /**
     * @param array $data
     * @return WhateverWasDone
     */
    public static function fromArray(array $data)
    {
        $event = new WhateverWasDone();
        $event->foo = $data['foo'];
        $event->bar = $data['bar'];

        return $event;
    }
}
