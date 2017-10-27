<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use Dayuse\Istorija\EventSourcing\EventSourcedObject;
use Dayuse\Istorija\Identifiers\Identifier;

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