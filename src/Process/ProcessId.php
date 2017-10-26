<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\Process;


use DayUse\Istorija\Identifiers\Identifier;
use DayUse\Istorija\Identifiers\PrefixedIdentifier;

class ProcessId extends PrefixedIdentifier
{
    static public function generateFromAggregate(string $processName, Identifier $aggregateId) : self
    {
        return self::fromString(self::prefix() . self::SEPARATOR . $processName . self::SEPARATOR . $aggregateId);
    }

    public static function generate()
    {
        throw new \InvalidArgumentException('This method could not be called. Use static::generateFromAggregate factory instead');
    }

    static protected function prefix()
    {
        return 'process';
    }
}