<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\DAO\Storage;


use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\Utils\Ensure;
use Doctrine\DBAL\Connection;

class DoctrineDAO implements DAOInterface, BulkableInterface
{
    use FunctionalTrait;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName  = $tableName;
    }

    /**
     * @param string $identifier
     *
     * @return array|null
     */
    public function find(string $identifier)
    {
        $key = $this->generateKey($identifier);

        $query = <<<MYSQL
SELECT * FROM `%s`
WHERE `key` = :key;
MYSQL;

        $record = $this->connection->fetchAssoc(
            sprintf($query, $this->tableName),
            [':key' => $key]
        );

        if (!$record) {
            return null;
        }

        return json_decode($record['value'], true);
    }

    public function remove(string $identifier)
    {
        $key = $this->generateKey($identifier);

        $query = <<<MYSQL
DELETE FROM `%s`
WHERE `key` = :key;
MYSQL;

        $this->connection->executeQuery(
            sprintf($query, $this->tableName),
            [':key' => $key]
        );

    }

    public function save(string $identifier, $data)
    {
        $key = $this->generateKey($identifier);

        if (null === $this->find($identifier)) {
            $this->connection->insert($this->tableName, [
                '`key`'   => $key,
                '`value`' => json_encode($data),
            ]);
        } else {
            $this->connection->update($this->tableName, [
                '`key`'   => $key,
                '`value`' => json_encode($data),
            ], [
                '`key`' => $key,
            ]);
        }
    }

    public function saveBulk(array $models)
    {
        Ensure::allIsInstanceOf($models, IdentifiableValue::class);

        /** @var IdentifiableValue $model */
        foreach ($models as $model) {
            $this->save($model->getId(), $model->getValue());
        }
    }

    public function flush()
    {
        $query = <<<MYSQL
TRUNCATE FROM `%s`
MYSQL;

        $this->connection->executeQuery(
            sprintf($query, $this->tableName)
        );
    }

    public function getCreateSql(): string
    {
        $query = <<<MYSQL
CREATE TABLE `%s` (
    `key` varchar(512) NOT NULL,
    `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
MYSQL;

        return sprintf($query, $this->tableName);
    }

    public function getDropSql(): string
    {
        $query = <<<MYSQL
DROP TABLE `%s`;
MYSQL;

        return sprintf($query, $this->tableName);
    }

    private function generateKey(string $identifier): string
    {
        return $identifier;
    }
}