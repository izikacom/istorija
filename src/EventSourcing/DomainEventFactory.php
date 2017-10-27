<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 17/10/2017
 * Time: 16:38
 */

namespace Dayuse\Istorija\EventSourcing;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use Dayuse\Istorija\EventStore\EventRecord;
use Dayuse\Istorija\EventStore\SlicedReadResult;

interface DomainEventFactory
{
    public function fromEventRecords(SlicedReadResult $slicedReadResult): DomainEventCollection;
    public function fromEventRecord(EventRecord $eventRecord): DomainEvent;
}