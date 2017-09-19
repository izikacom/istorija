<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

use DayUse\Istorija\Exception;

class AdvancedStorageNotAvailable extends \Exception implements Exception
{
    public static function forReadQuery(Storage $storage, AdvancedReadQuery $query)
    {
        return new self(sprintf('"%s" is not supported on "%s" implementation', get_class($query), get_class($storage)));
    }

    public static function onStorage(Storage $storage)
    {
        return new self(sprintf('Advanced Storage is not available on "%s" implementation', get_class($storage)));
    }
}
