<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Identifiers;

class GenericIdentifier implements Identifier
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $string)
    {
        return new self($string);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Identifier $other): bool
    {
        if (!$other instanceof $this) {
            return false;
        }

        return (string) $this === (string) $other;
    }
}
