<?php

namespace Dayuse\Istorija\EventStore\Exception;

use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Exception;

class EventPersistingOperationFailed extends \RuntimeException implements Exception
{
    public static function forUncommittedEvents(StreamName $toStream, array $uncommittedEvents, \Throwable $previous = null): self
    {
        return new self(sprintf(
            'Could not persist %s event(s) to stream %s',
            \count($uncommittedEvents),
            $toStream->getCanonicalStreamName()
        ), 0, $previous);
    }
}
