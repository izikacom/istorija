<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

use Dayuse\Istorija\EventStore\StreamName;

class ReadStreamEvents
{
    private $stream;
    private $start;
    private $count;
    private $readDirection;

    public static function forward(StreamName $stream, ?int $start, ?int $count): self
    {
        return new self($stream, $start, $count, 'ASC');
    }

    public function getSql()
    {
        $sql =<<<'SQL'
    SELECT *
      FROM %s
    WHERE 
      streamId = :streamId
    ORDER BY 
      checkpointNumber %s
    LIMIT %d OFFSET %d
    ;
SQL;

        return sprintf(
            $sql,
            Defaults::TABLE_NAME,
            strtoupper($this->readDirection),
            $this->count,
            $this->start
        );
    }

    public function getParameters(): array
    {
        return ['streamId' => (string) $this->stream->getIdentifier()];
    }

    private function __construct(StreamName $stream, ?int $start, ?int $count, string $dir)
    {
        $this->stream = $stream;
        $this->start = $start ?? 0;
        $this->count = $count ?? PHP_INT_MAX;
        $this->readDirection = $dir;
    }
}
