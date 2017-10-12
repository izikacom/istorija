<?php

namespace DayUse\Istorija\EventSourcing\DomainEvent;

use DayUse\Istorija\EventBus\Event;

/**
 * Something that happened in the past, that is of importance to the business.
 */
interface DomainEvent extends Event
{
}
