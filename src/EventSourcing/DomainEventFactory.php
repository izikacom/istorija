<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 17/10/2017
 * Time: 16:38
 */

namespace DayUse\Istorija\EventSourcing;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use DayUse\Istorija\EventStore\EventRecord;
use DayUse\Istorija\EventStore\SlicedReadResult;

interface DomainEventFactory
{
    public function fromEventRecords(SlicedReadResult $slicedReadResult): DomainEventCollection;
    public function fromEventRecord(EventRecord $eventRecord): DomainEvent;
}