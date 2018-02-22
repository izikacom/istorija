<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Projection;

use Dayuse\Istorija\Utils\Ensure;

class State
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): State
    {
        return new self(array_merge(
            $this->data,
            [
                $key => $value,
            ]
        ));
    }

    public function merge(array $data): State
    {
        return new self(array_merge(
            $this->data,
            $data
        ));
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function fromArray(array $data): State
    {
        return new self($data);
    }
}
