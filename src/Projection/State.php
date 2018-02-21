<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Projection;

use Dayuse\Istorija\Utils\Ensure;

class State
{
    /** @var array */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function inc(string $key, $step = 1, $initial = 0)
    {
        Ensure::numeric($step, sprintf('Could only increase value by numerical step, given: %s', $step));
        Ensure::numeric($initial, sprintf('Could only increase value from numerical, given: %s', $initial));

        $value = $this->get($key, $initial);

        Ensure::numeric($value, sprintf('Could only increase numeric value, given: %s', $value));

        return $this->set($key, $value + $step);
    }

    public function get(string $key, $default = null)
    {
        if (null === $key) {
            return $default;
        }

        if(0 === strpos($key, 'strict//')) {
            $strictKey = str_replace('strict//', '', $key);
            return $this->data[$strictKey] ?? $default;
        }

        $data = $this->data;
        foreach (explode('.', $key) as $segment) {
            if (!\is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }

            $data = $data[$segment];
        }

        return $data;
    }

    public function set(string $key, $value): State
    {
        if (null === $key) {
            return new self($this->data); // no modification occurred
        }

        if(0 === strpos($key, 'strict//')) {
            $strictKey = str_replace('strict//', '', $key);
            return new self(array_merge(
                $this->data, [
                    $strictKey => $value,
                ]
            ));
        }

        // used to avoid array reference
        $internalSet = function(array $data, string $key, $value) {
            $keys = explode('.', $key);

            $array = &$data;

            while (\count($keys) > 1) {
                $segment = array_shift($keys);
                // If the key doesn't exist at this depth, we will just create an empty array
                // to hold the next value, allowing us to create the arrays to hold final
                // values at the correct depth. Then we'll keep digging into the array.
                if (! isset($array[$segment]) || ! \is_array($array[$segment])) {
                    $array[$segment] = [];
                }
                $array = &$array[$segment];
            }
            $array[array_shift($keys)] = $value;

            return $data;
        };

        return new self($internalSet($this->data, $key, $value));
    }

    public function merge(array $data): State
    {
        return new self(array_merge(
            $this->data,
            $data
        ));
    }

    public function append(string $key, $value): State
    {
        $appendTo = function (array $data, $value): array {
            $data[] = $value;

            return $data;
        };

        $updatedKey = $appendTo($this->get($key, []), $value);

        return $this->set($key, $updatedKey);
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
