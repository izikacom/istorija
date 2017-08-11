<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\EventStore\Storage\DoctrineDbal\MysqlDbalStorage;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration as DbalConfiguration;

class ConfigurationBuilder
{
    private $storage;
    private $shouldInitializeStorage = false;

    public static function create(): self
    {
        return new self();
    }

    public function usingCustomStorage(Storage $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function usingMysqlDbalStorage(string $dsn): self
    {
        $dbal = DriverManager::getConnection(
            ['url' => $dsn],
            new DbalConfiguration()
        );

        $this->storage = new MysqlDbalStorage($dbal);

        return $this;
    }

    public function initializeStorageIfRequired(): self
    {
        $this->shouldInitializeStorage = true;

        return $this;
    }

    public function build(): Configuration
    {
        return new Configuration($this->storage, $this->shouldInitializeStorage);
    }

    private function __construct() {}
}
