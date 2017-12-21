<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;

/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class AbstractEventHandler implements EventHandler
{
    private const HANDLER_PREFIX = 'when';

    public function apply(DomainEvent $event) : void
    {
        $method = $this->methodNameResolver($event);

        if (!$this->supportEvent($event)) {
            throw new \InvalidArgumentException('Event handler does not support event');
        }

        $this->$method($event);
    }

    public function supportEvent(DomainEvent $event) : bool
    {
        $method = $this->methodNameResolver($event);

        return \is_callable([$this, $method]);
    }

    private function methodNameResolver(DomainEvent $event): string
    {
        return self::HANDLER_PREFIX . EventNameGuesser::guess($event);
    }
}
