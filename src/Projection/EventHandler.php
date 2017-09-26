<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 26/09/2017
 * Time: 09:36
 */

namespace DayUse\Istorija\Projection;


use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;

interface EventHandler
{
    public function apply(DomainEvent $event);
}