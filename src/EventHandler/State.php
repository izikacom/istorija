<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\EventHandler;

use Dayuse\Istorija\Utils\Ensure;

class State
{
    /** @var array */
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function append(string $key, $value): State
    {
        $appendTo = function (array $data, $value): array {
            $data[] = $value;

            return $data;
        };

        $updatedKeyValue = $appendTo($this->get($key, []), $value);

        return $this->set($key, $updatedKeyValue);
    }

    public function without(string $key, $removedValue): State
    {
        /** @var array $keyValue */
        $keyValue = $this->get($key);

        Ensure::isArray($keyValue);

        $updatedKeyValue = array_filter($keyValue, function($value) use($removedValue) {
            return $value !== $removedValue;
        });

        return $this->set($key, $updatedKeyValue);
    }

    public function get(string $key, $default = null)
    {
        if (null === $key) {
            return $default;
        }

        if(0 === strpos($key, '$')) {
            $absoluteKey = str_replace('$', '', $key);

            return $this->data[$absoluteKey] ?? $default;
        }

        $data = $this->all();
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
            return new self($this->all());
        }

        if(0 === strpos($key, '$')) {
            $absoluteKey = str_replace('$', '', $key);

            return $this->merge([
                $absoluteKey => $value,
            ]);
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

        $data = $this->all();

        return new self($internalSet($data, $key, $value));
    }

    public function filter(string $key, callable $filter): State
    {
        /** @var array $currentValue */
        $currentValue = $this->get($key);
        Ensure::isArray($currentValue);

        // if indexed array, do not preserved keys.
        $preservedKeys = array_values($currentValue) !== $currentValue;
        $filteredValue = array_filter($currentValue, $filter);

        return $this->set($key, $preservedKeys ? $filteredValue : array_values($filteredValue));
    }

    public function map(string $key, callable $callable): State
    {
        /** @var array $currentValue */
        $currentValue = $this->get($key);
        Ensure::isArray($currentValue);

        $updatedValue = array_map($callable, $currentValue);

        return $this->set($key, $updatedValue);
    }

    public function merge(array $data): State
    {
        return new static(array_merge(
            $this->data,
            $data
        ));
    }

    public function copy(): State
    {
        return new static($this->data);
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public static function createEmpty(): State
    {
        return new static();
    }

    public static function createFromState(State $state): State
    {
        return new static($state->all());
    }

    public static function createFromArray(array $data): State
    {
        return new static($data);
    }


    //
    // serialization functions
    //

    public function toArray(): array
    {
        return $this->data;
    }

    public static function fromArray(array $data): State
    {
        return new static($data);
    }
}
