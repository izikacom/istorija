<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

class ExecutionContext
{
    private $context = [];

    public function set(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    public function get(string $key)
    {
        if (isset($this->context[$key])) {

            throw ExecutionContextError::missingKey($key);
        }

        return $this->context[$key];
    }

    public function tryGet(string $key, $default = null)
    {
        return $this->context[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->context;
    }
}
