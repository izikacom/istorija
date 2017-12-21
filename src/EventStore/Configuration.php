<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore;

class Configuration
{
    private $storage;
    private $shouldInitializeStorage;

    public function __construct(Storage $storage, bool $shouldInitializeStorage = false)
    {
        $this->storage = $storage;
        $this->shouldInitializeStorage = $shouldInitializeStorage;
    }

    public function getStorage(): Storage
    {
        return $this->storage;
    }

    public function shouldInitializeStorage(): bool
    {
        return $this->shouldInitializeStorage;
    }
}
