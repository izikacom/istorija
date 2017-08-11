<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore\Storage\DoctrineDbal\Mysql\Queries;

final class PurgeStorage
{
    private $tableName;

    public static function table(?string $name = null)
    {
        return new self($name);
    }

    public function getSql(): string
    {
        return sprintf('DELETE FROM `%s`', $this->tableName);
    }

    public function __construct(?string $tableName = null)
    {
        $this->tableName = $tableName ?? Defaults::TABLE_NAME;
    }
}


