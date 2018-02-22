<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;

interface EventSourced
{
    public function recordThat(DomainEvent $event);
    public function reconstitute(DomainEventCollection $domainEvents);
}
