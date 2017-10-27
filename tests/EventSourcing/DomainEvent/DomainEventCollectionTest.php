<?php

namespace Dayuse\Test\Istorija\EventSourcing\DomainEvent;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use PHPUnit\Framework\TestCase;

class DomainEventCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_merge_appended_events()
    {
        $event1 = new WhateverWasDone('foo', 'bar');
        $event2 = new WhateverWasDone('fooo', 'baaar');
        $event3 = new WhateverWasDone('ffo', 'bbar');

        $collection = new DomainEventCollection([
            $event1,
            $event2,
        ]);

        $newCollection = $collection->append(new DomainEventCollection([
            $event3,
        ]));

        $this->assertEquals($event1, $newCollection[0]);
        $this->assertEquals($event2, $newCollection[1]);
        $this->assertEquals($event3, $newCollection[2]);
    }
}

class WhateverWasDone implements DomainEvent
{
    private $foo;
    private $bar;

    /**
     * WhateverWasDone constructor.
     *
     * @param $foo
     * @param $bar
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

