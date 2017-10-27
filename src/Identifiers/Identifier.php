<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Identifiers;

interface Identifier
{
    public static function fromString(string $string);

    public function __toString(): string;

    public function equals(Identifier $other): bool;
}
