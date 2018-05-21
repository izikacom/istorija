<?php

namespace Dayuse\Istorija\Identifiers;

use Ramsey\Uuid\Uuid;

abstract class UuidIdentifier implements Identifier, GeneratesIdentifier
{
    private $uuid;

    private function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return static
     */
    public static function generate()
    {
        return new static(Uuid::uuid4()->toString());
    }

    /**
     * @return static
     */
    public static function generateFrom(string $name)
    {
        return new static(Uuid::uuid5(Uuid::NAMESPACE_OID, $name)->toString());
    }

    /**
     * @return static
     */
    public static function fromString(string $uuid)
    {
        return new static($uuid);
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function equals(Identifier $identifier): bool
    {
        return ($identifier instanceof static) && ($identifier->uuid === $this->uuid);
    }
}
