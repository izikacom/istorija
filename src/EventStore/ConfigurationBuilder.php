<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore;

use Dayuse\Istorija\EventStore\Storage\DoctrineDbal\MySqlDbalStorage;
use Doctrine\DBAL\Configuration as DbalConfiguration;
use Doctrine\DBAL\DriverManager;

class ConfigurationBuilder
{
    private $storage;
    private $shouldInitializeStorage = false;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function usingCustomStorage(Storage $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function usingMySqlDbalStorage($data): self
    {
        if (is_string($data)) {
            $dsn  = $data;
            $dbal = DriverManager::getConnection(
                ['url' => $dsn],
                new DbalConfiguration()
            );
        } elseif (is_array($data)) {
            $params = $data;
            $dbal   = DriverManager::getConnection($params, new DbalConfiguration());
        } else {
            // @see http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html
            throw new \InvalidArgumentException('First parameter should be either a string to represent a DSN or an array to describe connection parameters');
        }


        $this->storage = new MySqlDbalStorage($dbal);

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
}
