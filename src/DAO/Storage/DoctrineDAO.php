<?php
namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\DAO\RequiresInitialization;
use Dayuse\Istorija\Utils\Ensure;
use Doctrine\DBAL\Connection;

class DoctrineDAO implements DAOInterface, BulkableInterface, RequiresInitialization
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

        return $this->deserialize($record['value']);
    }

    public function findBulk(array $identifiers): array
    {
        Ensure::allString($identifiers);

        $keys = array_map([$this, 'generateKey'], $identifiers);

        $query = <<<MYSQL
SELECT * FROM `%s`
WHERE `key` IN (:keys)
ORDER BY FIELD(`key`, :keys);
MYSQL;


        $records = $this->connection->fetchAll(
            sprintf($query, $this->tableName),
            ['keys' => $keys],
            ['keys' => Connection::PARAM_STR_ARRAY]
        );

        if(empty($records)) {
            return [];
        }

        return array_map(function($record) {
            return $this->deserialize($record['value']);
        }, $records);
    }

    public function remove(string $identifier) : void
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

    public function save(string $identifier, $data) : void
    {
        Ensure::isArray($data, 'DoctrineDAO was tested only with value as array');

        $key = $this->generateKey($identifier);


        if (null === $this->find($identifier)) {
            $query = sprintf('INSERT INTO `%s` (`key`, `value`) VALUES (:key, :value)', $this->tableName);
        } else {
            $query = sprintf('UPDATE `%s` SET `value` = :value WHERE `key` = :key', $this->tableName);
        }

        $this->connection->executeQuery(
            $query,
            [
                ':key'   => $key,
                ':value' => $this->serialize($data),
            ]
        );
    }

    public function saveBulk(array $models) : void
    {
        Ensure::allIsInstanceOf($models, IdentifiableValue::class);

        /** @var IdentifiableValue $model */
        foreach ($models as $model) {
            $this->save($model->getId(), $model->getValue());
        }
    }

    public function flush() : void
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
CREATE TABLE IF NOT EXISTS `%s` (
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

    public function initialize(): void
    {
        $this->connection->executeQuery($this->getCreateSql());
    }

    protected function generateKey(string $identifier): string
    {
        return $identifier;
    }

    protected function serialize($data): string
    {
        return json_encode($data);
    }

    protected function deserialize(string $value)
    {
        return json_decode($value, true);
    }

    final protected function getConnection(): Connection
    {
        return $this->connection;
    }
}
