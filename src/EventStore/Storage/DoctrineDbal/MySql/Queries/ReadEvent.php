<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore\Storage\DoctrineDbal\Mysql\Queries;

use DayUse\Istorija\EventStore\StreamName;

class ReadEvent
{
    private $stream;
    private $eventNumber;

    public static function fromStream(StreamName $stream, int $eventNumber)
    {
        return new self($stream, $eventNumber);
    }

    public function getSql()
    {
        $sql =<<<'SQL'
SELECT * FROM %s WHERE streamId = :streamId ORDER BY checkpointNumber ASC LIMIT 1 OFFSET %d;
SQL;

        return sprintf($sql, Defaults::TABLE_NAME, $this->eventNumber);
    }

    public function getParameters()
    {
        return [
            'streamId' => (string) $this->stream->getIdentifier()
        ];
    }

    private function __construct(StreamName $stream, int $eventNumber)
    {
        $this->stream = $stream;
        $this->eventNumber = $eventNumber;
    }
}
