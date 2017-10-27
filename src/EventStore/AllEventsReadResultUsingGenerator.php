<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore;

class AllEventsReadResultUsingGenerator extends AllEventsReadResult
{
    /** @var \Generator */
    private $wrappedGenerator;

    public function __construct(callable $wrappedGeneratorCallback, int $start, int $count)
    {
        
        parent::__construct([], $start, $count);
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
