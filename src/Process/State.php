<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\Utils\Ensure;

class State
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
        Ensure::allScalar($data);

        $this->data = $data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): State
    {
        Ensure::null($this->closedAt, 'This state have been already marked as done. Could not change data');
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
        Ensure::null($this->closedAt, 'This state have been already marked as done. Could not change data');
        Ensure::allScalar($data);

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

    public function isClosed(): bool
    {
        return null !== $this->closedAt;
    }

    public function toArray(): array
    {
        return [
            'data'     => $this->data,
            'closedAt' => $this->closedAt ? $this->closedAt->format(self::DATE_FORMAT) : null,
        ];
    }

    public static function fromArray(array $data): State
    {
        Ensure::allScalar($data['data']);

        $that           = new self();
        $that->data     = $data['data'];
        $that->closedAt = $data['closedAt'] ? \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $data['closedAt']) : null;

        return $that;
    }
}