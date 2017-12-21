<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\Identifiers\Identifier;
use Dayuse\Istorija\Identifiers\PrefixedIdentifier;

class ProcessId extends PrefixedIdentifier
{
    public static function generateFromIdentifier(string $processName, Identifier $aggregateId) : self
    {
        return self::fromString(self::prefix() . self::SEPARATOR . $processName . self::SEPARATOR . $aggregateId);
    }

    public static function generate()
    {
        throw new \InvalidArgumentException('This method could not be called. Use static::generateFromAggregate factory instead');
    }

    protected static function prefix()
    {
        return 'process';
    }
}
