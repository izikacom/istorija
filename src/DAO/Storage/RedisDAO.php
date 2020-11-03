<?php

namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\AdvancedDAOInterface;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\DAO\Pagination;
use Dayuse\Istorija\Utils\Ensure;

/**
 * Class RedisDAO
 *
 * This DAO store data on redis with prefixed keys.
 *
 *
 * @package Dayuse\Istorija\DAO\Storage
 */
class RedisDAO implements AdvancedDAOInterface, BulkableInterface
{
    use FunctionalTrait;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(\Redis $redis, string $prefix)
    {
        Ensure::notBlank($prefix);
        Ensure::eq(\Redis::SERIALIZER_NONE, $redis->getOption(\Redis::OPT_SERIALIZER),
            'Are you sure that your redis serializer is well configured?');

        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function find(string $identifier)
    {
        $key = $this->generateKey($identifier);

        if (false === $this->redis->exists($key)) {
            return null;
        }

        return $this->deserialize($this->redis->get($key));
    }

    /**
     * @inheritDoc
     */
    public function remove(string $identifier): void
    {
        $this->redis->del($this->generateKey($identifier));
    }

    /**
     * @inheritDoc
     */
    public function save(string $identifier, $data): void
    {
        $this->redis->set(
            $this->generateKey($identifier),
            $this->serialize($data)
        );
    }

    /**
     * @inheritDoc
     */
    public function flush(): void
    {
        $iterator = null;
        while (false !== ($keys = $this->redis->scan($iterator, $this->generateKey('*')))) {
            $this->redis->del($keys);
        }
    }

    public function findAll(Pagination $pagination): array
    {
        $keys = $this->keys();

        return array_map(
            [$this, 'deserialize'],
            $this->redis->getMultiple(\array_slice($keys, $pagination->getOffset(), $pagination->getMaxPerPage()))
        );
    }

    public function countAll(): int
    {
        return \count($this->keys());
    }

    protected function keys(): array
    {
        $iterator = null;
        $keys = [];

        while (false !== ($scanedKeys = $this->redis->scan($iterator, $this->generateKey('*')))) {
            $keys = array_merge(
                $keys,
                $scanedKeys
            );
        }

        return array_unique($keys);
    }


    /**
     * @inheritDoc
     */
    public function saveBulk(array $models): void
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
        $data = $this->redis->getMultiple($keys);

        return array_map([$this, 'deserialize'], $data);
    }


    final protected function getConnection(): \Redis
    {
        return $this->redis;
    }

    protected function generateKey(string $identifier): string
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
