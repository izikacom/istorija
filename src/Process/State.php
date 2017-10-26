<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\Process;

use DayUse\Istorija\Utils\Ensure;

class State
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * @var ProcessId
     */
    private $processId;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var \DateTimeInterface
     */
    private $doneAt;

    public function __construct(ProcessId $processId, array $data = [])
    {
        Ensure::allScalar($data);

        $this->processId = $processId;
        $this->data      = $data;
        $this->doneAt    = null;
    }

    public function get($key)
    {
        return $this->data[$key];
    }

    public function set($key, $value): void
    {
        Ensure::null($this->doneAt, 'This state have been already marked as done. Could not change data');
        Ensure::scalar($value);

        $this->data[$key] = $value;
    }

    public function merge(array $data): void
    {
        Ensure::null($this->doneAt, 'This state have been already marked as done. Could not change data');
        Ensure::allScalar($data);

        $this->data = array_merge($this->data, $data);
    }

    public function done(): void
    {
        Ensure::null($this->doneAt, 'This state have been already marked as done.');

        $this->doneAt = \DateTimeImmutable::createFromFormat(
            'U.u',
            sprintf('%.6F', microtime(true)),
            new \DateTimeZone('UTC')
        );
    }

    public function isDone(): bool
    {
        return null !== $this->doneAt;
    }

    public function getProcessId(): ProcessId
    {
        return $this->processId;
    }

    public function toArray()
    {
        return [
            'processId' => (string)$this->processId,
            'data'      => $this->data,
            'doneAt'    => $this->doneAt ? $this->doneAt->format(self::DATE_FORMAT) : null,
        ];
    }

    static public function fromArray(array $data)
    {
        Ensure::allScalar($data['data']);

        $that         = new self(ProcessId::fromString($data['processId']));
        $that->data   = $data['data'];
        $that->doneAt = $data['doneAt'] ? \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $data['doneAt']) : null;

        return $that;
    }
}