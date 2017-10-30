<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 08/11/2016
 * Time: 13:37
 */

namespace Dayuse\Istorija\DAO\Storage;


use Dayuse\Istorija\Utils\Ensure;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;

/**
 * Class RedisDAO
 *
 * This DAO store data on redis with prefixed keys.
 *
 *
 * @package Dayuse\Istorija\DAO\Storage
 */
class RedisDAO implements DAOInterface, BulkableInterface
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

    /**
     * RedisDictionary constructor.
     *
     * @param \Redis $redis
     * @param string $prefix
     */
    public function __construct(\Redis $redis, string $prefix)
    {
        Ensure::notBlank($prefix);
        Ensure::eq(\Redis::SERIALIZER_PHP, $redis->getOption(\Redis::OPT_SERIALIZER), 'Are you sure that your redis serializer is well configured?');

        $this->redis  = $redis;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function find(string $identifier)
    {
        $key  = $this->generateKey($identifier);
        $data = $this->redis->get($key);

        if (false === $data) {
            return null;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $identifier)
    {
        $this->redis->del($this->generateKey($identifier));
    }

    /**
     * @inheritDoc
     */
    public function save(string $identifier, $data)
    {
        $this->redis->set($this->generateKey($identifier), $data);
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        $iterator = null;
        while (false !== ($keys = $this->redis->scan($iterator, $this->generateKey('*')))) {
            $this->redis->del($keys);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveBulk(array $models)
    {
        Ensure::allIsInstanceOf($models, IdentifiableValue::class);

        /** @var IdentifiableValue $model */
        foreach ($models as $model) {
            $this->save($model->getId(), $model->getValue());
        }
    }

    /**
     * default : dictionary:{identifier}
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function generateKey(string $identifier)
    {
        return sprintf('%s:%s', $this->prefix, $identifier);
    }
}