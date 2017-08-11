<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

class AllEventsReadResult implements \Iterator, \Countable
{
    private $events;
    private $start;
    private $count;

    public function __construct(array $events, int $start, int $count)
    {
        $this->events = $events;
        $this->start = $start;
        $this->count = $count;
    }

    public function current(): EventRecord
    {
        return current($this->events);
    }

    public function next()
    {
        next($this->events);
    }

    public function key()
    {
        return key($this->events);
    }

    public function valid(): bool
    {
        return (null !== key($this->events));
    }

    public function rewind()
    {
        reset($this->events);
    }

    public function count(): int
    {
        return $this->count;
    }

    public function getStartingCheckpointPosition()
    {
        return $this->start;
    }
}
