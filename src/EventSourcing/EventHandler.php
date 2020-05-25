<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;

/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
interface EventHandler
{
    public function apply(DomainEvent $event) : void;

    public function supportEvent(DomainEvent $event) : bool;

    public function supportedEventClasses(): array;
}
