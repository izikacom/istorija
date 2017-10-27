<?php

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

use Dayuse\Istorija\EventBus\Event;

/**
 * Something that happened in the past, that is of importance to the business.
 */
interface DomainEvent extends Event
{
}
