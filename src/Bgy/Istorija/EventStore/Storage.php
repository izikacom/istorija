<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

interface Storage
{
    public function persist(StreamName $stream, array $uncommittedEvents, ?int $expectedVersion): void;
    public function delete(StreamName $stream, int $expectedVersion): void;
    public function readEvent(StreamName $stream, int $eventNumber): EventRecord;
    public function readStreamEvents(StreamName $stream, int $start = 0, int $count = PHP_INT_MAX): SlicedReadResult;
    public function readAllEvents(int $start = 0, int $count = PHP_INT_MAX): AllEventsReadResult;
}
