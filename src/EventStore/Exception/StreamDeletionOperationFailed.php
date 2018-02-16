<?php

namespace Dayuse\Istorija\EventStore\Exception;

use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Exception;

class StreamDeletionOperationFailed extends \RuntimeException implements Exception
{
    public static function streamNotFound(StreamName $streamName): self
    {
        return new self(sprintf('Could not delete not found stream: %s', $streamName->getCanonicalStreamName()));
    }
}
