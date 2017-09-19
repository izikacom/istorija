<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore\Storage;

use DayUse\Istorija\Exception;

class OptimisticConcurrencyFailed extends \Exception implements Exception
{
    public static function versionDoesNotMatch($expectedVersion, $actualVersion)
    {
        $message = sprintf('Expected streamVersion = %d, got %d', $expectedVersion, $actualVersion);

        return new self($message);
    }
}
