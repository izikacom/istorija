<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Identifiers;

use Dayuse\Istorija\Utils\Ensure;
use Ramsey\Uuid\Uuid;

abstract class UuidIdentifier implements Identifier, GeneratesIdentifier
{
    private $uuid;

    public static function generate()
    {
        return new static(Uuid::uuid4()->toString());
    }

    public static function generateFrom(string $name)
    {
        return new static(Uuid::uuid5(Uuid::NAMESPACE_OID, $name)->toString());
    }

    public static function fromString(string $uuid)
    {
        return new static($uuid);
    }

    public function __toString(): string
    {
        return (string) $this->uuid;
    }

    public function equals(Identifier $identifier): bool
    {
        return ($identifier instanceof static) && ($identifier->uuid === $this->uuid);
    }

    private function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }
}
