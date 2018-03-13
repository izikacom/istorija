<?php

namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\AdvancedDAOInterface;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\Utils\Ensure;
use Predis\Client;
use Predis\Collection\Iterator;

/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class PredisDAO implements AdvancedDAOInterface, BulkableInterface
{
    use FunctionalTrait;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(Client $redis, string $prefix)
    {
        Ensure::notBlank($prefix);

        $this->redis  = $redis;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function find(string $identifier)
    {
        $key  = $this->generateKey($identifier);

        if (0 === $this->redis->exists($key)) {
            return null;
        }

        return $this->unserialize($this->redis->get($key));
    }

    /**
     * @inheritDoc
     */
    public function remove(string $identifier) : void
    {
        $this->redis->del([
            $this->generateKey($identifier)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function save(string $identifier, $data) : void
    {
        $this->redis->set(
            $this->generateKey($identifier),
            $this->serialize($data)
        );
    }

    /**
     * @inheritDoc
     */
    public function flush() : void
    {
        foreach (new Iterator\Keyspace($this->redis, $this->generateKey('*')) as $key) {
            $this->redis->del([$key]);
        }
    }

    public function findAll(int $page = 0, int $maxPerPage = 50): array
    {
        $iterator = new \LimitIterator(
            new Iterator\Keyspace($this->redis, $this->generateKey('*')),
            $page,
            $maxPerPage
        );

        return array_map(
            [$this, 'unserialize'],
            iterator_to_array($iterator)
        );
    }

    public function countAll(): int
    {
        return iterator_count(new Iterator\Keyspace($this->redis, $this->generateKey('*')));
    }

    /**
     * @inheritDoc
     */
    public function saveBulk(array $models) : void
    {
        Ensure::allIsInstanceOf($models, IdentifiableValue::class);

        /** @var IdentifiableValue $model */
        foreach ($models as $model) {
            $this->save($model->getId(), $model->getValue());
        }
    }

    final protected function getConnection(): client
    {
        return $this->redis;
    }

    protected function generateKey(string $identifier) : string
    {
        return sprintf('%s:%s', $this->prefix, $identifier);
    }

    protected function serialize(array $data): string
    {
        return json_encode($data);
    }

    protected function unserialize(string $value): array
    {
        return json_decode($value, true);
    }
}