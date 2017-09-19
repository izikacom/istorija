<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

class SlicedReadResult implements \Iterator, \Countable
{
    private $stream;
    private $events;
    private $start;
    private $count;

    public function __construct(StreamName $stream, array $events, int $start, int $count)
    {
        $this->stream = $stream;
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

    public function getStartingEventNumber()
    {
        return $this->start;
    }

    public function getStream(): StreamName
    {
        return $this->stream;
    }
}
