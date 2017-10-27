<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 17/10/2017
 * Time: 16:40
 */

namespace Dayuse\Istorija\EventSourcing;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;

interface EventEnvelopeFactory
{
    public function fromDomainEvents(DomainEventCollection $domainEvents): array;
}