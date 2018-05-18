<?php
namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;

interface EventEnvelopeFactory
{
    public function fromDomainEvents(DomainEventCollection $domainEvents, array $additionalMetadata = []): array;
}
