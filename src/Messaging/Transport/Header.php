<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging\Transport;

use Dayuse\Istorija\Utils\Ensure;

class Header
{
    private $name;
    private $value;

    public function __construct(string $name, $value)
    {
        Ensure::scalar($value, 'Header $value must be a scalar');
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return sprintf('%s: %s', $this->name, (string) $this->value);
    }
}
