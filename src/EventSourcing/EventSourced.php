<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;

interface EventSourced
{
    public function recordThat(DomainEvent $event);
    public function reconstitute(DomainEventCollection $domainEvents);
}
