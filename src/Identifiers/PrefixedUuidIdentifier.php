<?php

namespace Dayuse\Istorija\Identifiers;

use Ramsey\Uuid\Uuid;

abstract class PrefixedUuidIdentifier extends PrefixedIdentifier
{
    public static function getCanonicalMatchingPattern(): string
    {
        return sprintf(
            '%s%s(?P<identifier>%s)',
            static::prefix(),
            self::SEPARATOR,
            trim(Uuid::VALID_PATTERN, '^$')
        );
    }

    /**
     * @return static
     */
    public static function generate()
    {
        return parent::fromString(static::prefix() . self::SEPARATOR . GenericUuidIdentifier::generate());
    }

    /**
     * @return static
     */
    protected static function generateFrom(string $name)
    {
        return parent::fromString(static::prefix() . self::SEPARATOR . GenericUuidIdentifier::generateFrom($name));
    }
}
