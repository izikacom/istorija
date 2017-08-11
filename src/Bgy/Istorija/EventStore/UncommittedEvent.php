<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\Utils\Contract;

class UncommittedEvent
{
    private $eventId;
    private $contract;
    private $data;
    private $metadata;

    public function __construct(EventId $id, Contract $contract, EventData $data, ?EventMetadata $metadata)
    {
        $this->eventId = $id;
        $this->contract = $contract;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function getEventId(): EventId
    {
        return $this->eventId;
    }

    public function getData(): EventData
    {
        return $this->data;
    }

    public function getMetadata(): ?EventMetadata
    {
        return $this->metadata;
    }
}
