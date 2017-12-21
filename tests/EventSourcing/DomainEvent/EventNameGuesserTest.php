<?php

namespace Dayuse\Test\Istorija\EventSourcing\DomainEvent;

use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use PHPUnit\Framework\TestCase;

class EventNameGuesserTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_guess_event_name_correctly()
    {
        $event1 = new WhateverWasDone('foo', 'bar');

        $this->assertEquals('WhateverWasDone', EventNameGuesser::guess($event1));
    }
}
