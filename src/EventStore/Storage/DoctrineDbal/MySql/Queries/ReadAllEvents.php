<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

class ReadAllEvents
{
    private $start;
    private $count;
    private $readDirection;

    public static function forward(?int $start, ?int $count): self
    {
        return new self($start, $count, 'ASC');
    }

    public function getSql()
    {
        $sql =<<<'SQL'
    SELECT *
      FROM %s
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
        return [];
    }

    private function __construct(?int $start, ?int $count, string $dir)
    {
        $this->start = $start ?? 0;
        $this->count = $count ?? PHP_INT_MAX;
        $this->readDirection = $dir;
    }
}
