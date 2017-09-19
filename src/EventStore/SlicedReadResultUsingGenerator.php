<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

class SlicedReadResultUsingGenerator extends SlicedReadResult
{
    /** @var \Generator */
    private $wrappedGenerator;

    public function __construct(StreamName $stream, callable $wrappedGeneratorCallback, int $start, int $count)
    {
        parent::__construct($stream, [], $start, $count);
        $this->wrappedGenerator = $wrappedGeneratorCallback();
    }

    public function current(): EventRecord
    {
        return $this->wrappedGenerator->current();
    }

    public function next()
    {
        $this->wrappedGenerator->next();
    }

    public function key()
    {
        return $this->wrappedGenerator->key();
    }

    public function valid(): bool
    {
        return $this->wrappedGenerator->valid();
    }

    public function rewind()
    {
        $this->wrappedGenerator->rewind();
    }
}
