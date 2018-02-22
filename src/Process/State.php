<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\Utils\Ensure;
use Dayuse\Istorija\Utils\StateInterface;

class State implements StateInterface
{
    private const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @var array
     */
    private $data;

    /**
     * @var \DateTimeInterface
     */
    private $closedAt;

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
        Ensure::null($this->closedAt, 'This state have been already marked as done. Could not change data');

        return new self(array_merge(
            $this->data,
            [
                $key => $value,
            ]
        ));
    }

    public function merge(array $data): StateInterface
    {
        Ensure::null($this->closedAt, 'This state have been already marked as done. Could not change data');

        return new self(array_merge(
            $this->data,
            $data
        ));
    }

    public function close(): State
    {
        Ensure::null($this->closedAt, 'This state have been already marked as done.');

        $that = new self($this->data);

        $that->closedAt = \DateTimeImmutable::createFromFormat(
            'U.u',
            sprintf('%.6F', microtime(true)),
            new \DateTimeZone('UTC')
        );

        return $that;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function isClosed(): bool
    {
        return null !== $this->closedAt;
    }

    public function copy(): StateInterface
    {
        return new self($this->data);
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
        return [
            'data'     => $this->data,
            'closedAt' => $this->closedAt ? $this->closedAt->format(self::DATE_FORMAT) : null,
        ];
    }

    public static function fromArray(array $data): StateInterface
    {
        $that           = new self();
        $that->data     = $data['data'];
        $that->closedAt = $data['closedAt'] ? \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $data['closedAt']) : null;

        return $that;
    }
}
