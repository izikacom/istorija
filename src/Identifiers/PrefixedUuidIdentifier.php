<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 11/09/2017
 * Time: 10:38
 */

namespace DayUse\Istorija\Identifiers;


use Assert\Assertion;

abstract class PrefixedUuidIdentifier implements Identifier, GeneratesIdentifier
{
    /**
     * @var UuidIdentifier
     */
    private $identifier;

    /**
     * OpaqueIdentifier constructor.
     *
     * @param UuidIdentifier $identifier
     */
    private function __construct(UuidIdentifier $identifier)
    {
        Assertion::regex(
            static::prefix(),
            sprintf('/^(%s)$/i', static::prefixPattern()),
            sprintf("Prefix should be letter composed only and 100 max length, pattern : %s, prefix : %s", static::prefixPattern(), static::prefix())
        );

        $this->identifier = $identifier;
    }

    abstract static protected function prefix();

    /**
     * Used to avoid human error when writing implementation.
     * See the assertion from constructor.
     *
     * @return string
     */
    static final private function prefixPattern()
    {
        return '[a-z\-]{0,100}';
    }

    static final public function isMatchingPattern(string $string): bool
    {
        $uuidPattern   = "[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}";
        $opaquePattern = sprintf('/^(%s)-(%s)$/i',
            static::prefix(),
            $uuidPattern
        );

        return 1 === preg_match($opaquePattern, $string, $strings);
    }

    static final private function extractUuidFromString(string $string): string
    {
        $uuidPattern   = "[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}";
        $opaquePattern = sprintf('/^(%s)-(%s)$/i',
            static::prefix(),
            $uuidPattern
        );

        preg_match($opaquePattern, $string, $strings);

        return $strings[2];
    }

    public static function generate()
    {
        return new static(GenericUuidIdentifier::generate());
    }

    public static function fromString(string $string)
    {
        if (!static::isMatchingPattern($string)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not matching pattern : %s-{uuid}", $string, static::prefix()));
        }

        $uuid = self::extractUuidFromString($string);

        return new static(GenericUuidIdentifier::fromString($uuid));
    }

    public function __toString(): string
    {
        return sprintf('%s-%s', static::prefix(), $this->identifier);
    }

    public function equals(Identifier $other): bool
    {
        if (!($other instanceof PrefixedUuidIdentifier)) {
            return false;
        }

        if (static::prefix() !== $other->prefix()) {
            return false;
        }

        return (string)$this === (string)$other;
    }
}