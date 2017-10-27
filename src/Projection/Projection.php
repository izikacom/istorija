<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 26/09/2017
 * Time: 15:18
 */

namespace Dayuse\Istorija\Projection;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventStore\EventMetadata;

interface Projection
{
    public function apply(DomainEvent $event);
    public function reset();
}