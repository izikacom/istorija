<?php

namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\AdvancedDAOInterface;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\DAO\Pagination;
use Dayuse\Istorija\Utils\Ensure;
use Predis\Client;
use Predis\Collection\Iterator;

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

        return $this->deserialize($this->redis->get($key));
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

    public function findAll(Pagination $pagination): array
    {
        $iterator = new \LimitIterator(
            new Iterator\Keyspace($this->redis, $this->generateKey('*')),
            $pagination->getPage(),
            $pagination->getMaxPerPage()
        );

        return array_map([$this, 'deserialize'], iterator_to_array($iterator));
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

    public function findBulk(array $identifiers): array
    {
        Ensure::allString($identifiers);

        $keys = array_map([$this, 'generateKey'], $identifiers);
        $data = $this->redis->mget($keys);

        return array_map([$this, 'deserialize'], $data);
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

    protected function deserialize(string $value): array
    {
        return json_decode($value, true);
    }
}