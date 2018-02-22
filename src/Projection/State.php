<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Projection;

use Dayuse\Istorija\Utils\StateInterface;

class State implements StateInterface
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): StateInterface
    {
        return new self(array_merge(
            $this->data,
            [
                $key => $value,
            ]
        ));
    }

    public function merge(array $data): StateInterface
    {
        return new self(array_merge(
            $this->data,
            $data
        ));
    }

    public function copy(): StateInterface
    {
        return new self($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public static function createEmpty(): StateInterface
    {
        return new self([]);
    }

    public static function createFromArray(array $data): StateInterface
    {
        return new self($data);
    }


    //
    // serialization functions
    //

    public function toArray(): array
    {
        return $this->data;
    }

    public static function fromArray(array $data): StateInterface
    {
        return new self($data);
    }
}
