<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 26/09/2017
 * Time: 15:18
 */

namespace DayUse\Istorija\Projection;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventStore\EventMetadata;

interface Projection
{
    public function apply(DomainEvent $event);
    public function reset();
}