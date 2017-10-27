<?php

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

class DomainEventCollection implements \Countable, \Iterator, \ArrayAccess
{
    /**
     * @var DomainEvent[]|array
     */
    private $events = [];

    /**
     * @param DomainEvent[]|array $domainEvents
     * @throws \InvalidArgumentException
     */
    public function __construct(array $domainEvents)
    {
        foreach ($domainEvents as $domainEvent) {
            if (!$domainEvent instanceof DomainEvent) {
                throw new \InvalidArgumentException("DomainEvent expected");
            }
            $this->events[] = $domainEvent;
        }
    }

    /**
     * @param DomainEventCollection $other
     * @return DomainEventCollection
     */
    public function append(DomainEventCollection $other)
    {
        return new DomainEventCollection(array_merge($this->events, iterator_to_array($other)));
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * @return DomainEvent
     */
    public function current()
    {
        return current($this->events);
    }

    /**
     * @return int
     */
    public function key()
    {
        return key($this->events);
    }

    /**
     * @return void
     */
    public function next()
    {
        next($this->events);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset($this->events);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return null !== key($this->events);
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return null !==$this->events[$offset];
    }

    /**
     * @param int $offset
     * @return DomainEvent
     */
    public function offsetGet($offset)
    {
        return $this->events[$offset];
    }

    /**
     * @throws DomainEventsAreImmutable
     */
    public function offsetSet($offset, $value)
    {
        throw new DomainEventsAreImmutable();
    }

    /**
     * @throws DomainEventsAreImmutable
     */
    final public function offsetUnset($offset)
    {
        throw new DomainEventsAreImmutable();
    }

    public function map(Callable $callback)
    {
        return array_map($callback, $this->events);
    }
}
