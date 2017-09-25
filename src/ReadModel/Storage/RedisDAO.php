<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 08/11/2016
 * Time: 13:37
 */

namespace DayUse\Istorija\ReadModel\Storage;


use DayUse\Istorija\Utils\Ensure;
use DayUse\Istorija\ReadModel\BulkableInterface;
use DayUse\Istorija\ReadModel\DAOInterface;
use DayUse\Istorija\ReadModel\FunctionalTrait;
use DayUse\Istorija\ReadModel\IdentifiableValue;

/**
 * Class RedisDAO
 *
 * This DAO store data on redis with prefixed keys.
 *
 *
 * @package DayUse\Istorija\ReadModel\Storage
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
        $this->redis  = $redis;
        $this->prefix = $prefix;

        // TODO - This is very dangerous to set this option right here. How to improve?
        $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
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
    public function save(string $identifier, array $data)
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