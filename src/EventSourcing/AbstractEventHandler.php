<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use InvalidArgumentException;
use function is_callable;

class AbstractEventHandler implements EventHandler
{
    use EventHandlerNameResolverUsingNameGuesser;

    public const HANDLER_PREFIX = 'when';

    public function apply(DomainEvent $event): void
    {
        $method = $this->methodNameResolver($event);

        if (!$this->supportEvent($event)) {
            throw new InvalidArgumentException('Event handler does not support event');
        }

        $this->$method($event);
    }

    public function supportEvent(DomainEvent $event): bool
    {
        $method = $this->methodNameResolver($event);

        return is_callable([$this, $method]);
    }
}
