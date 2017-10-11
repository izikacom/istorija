<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

class ExecutionContext
{
    private $context = [];

    public function set(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->context[$key];
    }

    public function all(): array
    {
        return $this->context;
    }
}
