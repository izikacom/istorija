<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore\Storage\DoctrineDbal\Mysql\Queries;

use Bgy\Istorija\EventStore\StreamName;

class SelectAndSetCurrentStreamVersion
{
    private $stream;

    public function __construct(StreamName $stream)
    {
        $this->stream = $stream;
    }

    public function getSql()
    {
        $sql =<<<'SQL'
SELECT 
  @currentStreamVersion := COUNT(streamId) 
FROM %s
WHERE 
  streamId = :streamId
;
SQL;
        return sprintf($sql, Defaults::TABLE_NAME);
    }

    public function getParameters()
    {
        return ['streamId' => $this->stream->getIdentifier()];
    }
}
