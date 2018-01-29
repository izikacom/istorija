<?php

namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\Utils\Ensure;
use Predis\Client;


/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class PredisDAO implements DAOInterface, BulkableInterface
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

        return $this->denormalize($this->redis->get($key));
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
            $this->normalize($data)
        );
    }

    /**
     * @inheritDoc
     */
    public function flush() : void
    {
        $iterator = null;
        while (false !== ($keys = $this->redis->scan($iterator, $this->generateKey('*')))) {
            $this->redis->del($keys);
        }
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

    protected function normalize(array $data): string
    {
        return json_encode($data);
    }

    protected function denormalize(string $value): array
    {
        return json_decode($value, true);
    }
}