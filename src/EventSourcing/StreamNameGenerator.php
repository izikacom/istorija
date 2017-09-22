<?php

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventStore\StreamName;
use DayUse\Istorija\Identifiers\Identifier;
use DayUse\Istorija\Utils\Contract;

class StreamNameGenerator
{
    public static function fromAggregate(AggregateRoot $aggregateRoot, Identifier $aggregateId)
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