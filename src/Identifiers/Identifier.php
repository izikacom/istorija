<?php

namespace Dayuse\Istorija\Identifiers;

interface Identifier
{
    /**
     * @return static
     */
    public static function fromString(string $string);

    public function __toString(): string;

    public function equals(Identifier $other): bool;
}
