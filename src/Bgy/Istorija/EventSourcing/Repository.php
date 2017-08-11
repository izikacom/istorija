<?php

namespace Bgy\Istorija\EventSourcing;

use Bgy\Istorija\EventStore\EventStore;
use Bgy\Istorija\Utils\Contract;
use Bgy\Istorija\Utils\NotImplemented;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
abstract class Repository
{
    protected $eventStore;
    protected $modelContract;
    protected $eventMetadataFactory;

    public function __construct(EventStore $eventStore, Contract $modelContract) {
        $this->eventStore = $eventStore;
        $this->modelContract = $modelContract;
    }

    protected function doGet($id)
    {
        throw NotImplemented::method(__METHOD__);
    }

    protected function doSave($aggregateRoot)
    {
        throw NotImplemented::method(__METHOD__);
    }
}
