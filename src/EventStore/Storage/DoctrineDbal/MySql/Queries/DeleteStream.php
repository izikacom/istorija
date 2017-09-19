<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

use DayUse\Istorija\EventStore\StreamName;

class DeleteStream
{
    private $stream;

    public function __construct(StreamName $stream)
    {
        $this->stream = $stream;
    }

    public function getSql(): string
    {
        $sql =<<<'SQL'
DELETE FROM `%s` WHERE streamId LIKE :streamId;
SQL;

        return sprintf($sql, Defaults::TABLE_NAME);
    }

    public function getParameters(): array
    {
        return [
            'streamId' => (string) $this->stream->getIdentifier(),
        ];
    }
}
