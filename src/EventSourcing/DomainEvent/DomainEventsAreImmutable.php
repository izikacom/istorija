<?php

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

use Dayuse\Istorija\Exception;

class DomainEventsAreImmutable extends \DomainException implements Exception
{
}
