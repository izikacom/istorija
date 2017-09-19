<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

use DayUse\Istorija\Utils\Contract;

class EventEnvelope
{
    private $contract;
    private $eventData;
    private $eventMetadata;

    public static function wrap(Contract $contract, EventData $data, ?EventMetadata $metadata)
    {
        return new self($contract, $data, $metadata);
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function getEventData(): EventData
    {
        return $this->eventData;
    }

    public function getEventMetadata(): ?EventMetadata
    {
        return $this->eventMetadata;
    }

    public function __construct(Contract $contract, EventData $eventData, ?EventMetadata $eventMetadata)
    {
        $this->contract = $contract;
        $this->eventData = $eventData;
        $this->eventMetadata = $eventMetadata;
    }
}
