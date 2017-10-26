<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\Process;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use DayUse\Istorija\EventSourcing\EventSourcedObject;
use DayUse\Istorija\Identifiers\Identifier;

abstract class Process
{
    public function apply(DomainEvent $event) : void
    {
        $method = 'when' . EventNameGuesser::guess($event);

        if(!$this->supportEvent($event)) {
            throw new \InvalidArgumentException('Process does not support event');
        }

        call_user_func([$this, $method], $event);
    }

    public function supportEvent(DomainEvent $event) : bool
    {
        $method = 'when' . EventNameGuesser::guess($event);

        return is_callable([$this, $method]);
    }

    final public function getProcessIdFromAggregate(Identifier $identifier) : ProcessId
    {
        return ProcessId::generateFromAggregate($this->getName(), $identifier);
    }

    abstract public function getName(): string;
}