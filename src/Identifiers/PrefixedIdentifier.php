<?php

namespace Dayuse\Istorija\Identifiers;

abstract class PrefixedIdentifier implements Identifier, GeneratesIdentifier
{
    protected const SEPARATOR = '-';

    private $value;

    abstract protected static function prefix(): string;

    protected function __construct(string $value)
    {
        if (!static::isMatchingPattern($value)) {
            throw new \InvalidArgumentException(sprintf("'%s' does not match pattern: %s", $value, static::getCanonicalMatchingPattern()));
        }

        $this->value = $value;
    }

    public static function getCanonicalMatchingPattern(): string
    {
        return sprintf('%s%s(?P<identifier>.+)', static::prefix(), self::SEPARATOR);
    }

    final public static function isMatchingPattern(string $string): bool
    {
        return 1 === preg_match('#^'.static::getCanonicalMatchingPattern().'$#i', $string);
    }

    /**
     * @retun static
     */
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
        if (!$other instanceof PrefixedIdentifier) {
            return false;
        }

        return (string) $this === (string) $other;
    }
}
