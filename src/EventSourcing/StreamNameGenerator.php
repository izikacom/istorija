<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventStore\StreamName;
use Dayuse\Istorija\Identifiers\Identifier;
use Dayuse\Istorija\Utils\Contract;

class StreamNameGenerator
{
    public static function fromAggregate(AbstractAggregateRoot $aggregateRoot, Identifier $aggregateId)
    {
        $class = trim(get_class($aggregateRoot), "\\");
        $parts = explode("\\", $class);
        $type = end($parts);
        
        return new StreamName(
            $aggregateId,
            Contract::with($type)
        );
    }

    public static function fromAggregateClassName($aggregateClassName, Identifier $aggregateId)
    {
        $class = trim($aggregateClassName, "\\");
        $parts = explode("\\", $class);
        $type = end($parts);

        return new StreamName(
            $aggregateId,
            Contract::with($type)
        );
    }
}
