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
        Ensure::allScalar($data);

        $this->data = $data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): State
    {
        Ensure::scalar($value);

        return new self(array_merge(
            $this->data,
            [
                $key => $value,
            ]
        ));
    }

    public function merge(array $data): State
    {
        Ensure::allScalar($data);

        return new self(array_merge(
            $this->data,
            $data
        ));
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function fromArray(array $data): State
    {
        Ensure::allScalar($data['data']);

        return new self($data['data']);
    }
}