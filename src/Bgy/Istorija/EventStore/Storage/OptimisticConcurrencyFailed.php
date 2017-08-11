<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore\Storage;

use Bgy\Istorija\Exception;

class OptimisticConcurrencyFailed extends \Exception implements Exception
{
    public static function versionDoesNotMatch($expectedVersion, $actualVersion)
    {
        $message = sprintf('Expected streamVersion = %d, got %d', $expectedVersion, $actualVersion);

        return new self($message);
    }
}
