<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

class JsonPayloadProperty
{
    public const TYPE_SCALAR = 'scalar';
    public const TYPE_ARRAY  = 'array';
    public const TYPE_OBJECT = 'object';

    private $propertyName, $type;

    public function __construct(string $propertyName, string $type)
    {
        Ensure::choice($type, [self::TYPE_SCALAR, self::TYPE_OBJECT, self::TYPE_ARRAY]);

        $this->propertyName = $propertyName;
        $this->type = $type;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
