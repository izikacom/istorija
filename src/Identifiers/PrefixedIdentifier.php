<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 11/09/2017
 * Time: 10:38
 */

namespace Dayuse\Istorija\Identifiers;

abstract class PrefixedIdentifier implements Identifier, GeneratesIdentifier
{
    protected const SEPARATOR = '-';

    /**
     * @var string
     */
    private $value;

    abstract protected static function prefix();

    protected function __construct(string $value)
    {
        if (!static::isMatchingPattern($value)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not matching pattern : %s%s{identifier}", $value, static::prefix(), static::SEPARATOR));
        }

        $this->value = $value;
    }

    final public static function isMatchingPattern(string $string): bool
    {
        $prefixPattern = sprintf(
            '/^(%s)%s/i',
            static::prefix(),
            self::SEPARATOR
        );

        return 1 === preg_match($prefixPattern, $string, $strings);
    }

    public static function fromString(string $string)
    {
        return new static($string);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Identifier $other): bool
    {
        if (!($other instanceof PrefixedIdentifier)) {
            return false;
        }

        return (string)$this === (string)$other;
    }
}
