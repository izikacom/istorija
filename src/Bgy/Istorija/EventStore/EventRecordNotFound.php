<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\Exception;

class EventRecordNotFound extends \Exception implements Exception
{
    static public function onStream(StreamName $stream, int $eventNumber)
    {
        return new self(
            sprintf(
                'Event Number %d not found on Stream %s (%s)',
                $eventNumber,
                $stream->getIdentifier(),
                $stream->getContract()
            )
        );
    }
}
