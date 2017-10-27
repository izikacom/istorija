<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 11/09/2017
 * Time: 10:38
 */

namespace Dayuse\Istorija\Identifiers;


abstract class PrefixedUuidIdentifier extends PrefixedIdentifier
{
    public static function generate()
    {
        return parent::fromString(static::prefix() . self::SEPARATOR . GenericUuidIdentifier::generate());
    }
}