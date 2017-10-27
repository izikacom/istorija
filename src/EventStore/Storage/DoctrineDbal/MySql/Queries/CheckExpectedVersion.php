<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySql\Queries;

class CheckExpectedVersion
{
    private $expectedVersion;

    public function __construct(int $expectedVersion)
    {
        $this->expectedVersion = $expectedVersion;
    }

    public function getSql(): string
    {
        $sql =<<<'SQL'
SELECT 
  CASE @currentStreamVersion 
    WHEN :expectedVersion 
    THEN TRUE 
  ELSE
    ES_OPTIMISITIC_CONCURRENCY_FAILED()
END;
SQL;

        return $sql;
    }

    public function getParameters(): array
    {
        return ['expectedVersion' => $this->expectedVersion];
    }
}
