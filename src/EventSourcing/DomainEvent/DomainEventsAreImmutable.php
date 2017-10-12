<?php

namespace DayUse\Istorija\EventSourcing\DomainEvent;

use DayUse\Istorija\Exception;

class DomainEventsAreImmutable extends \DomainException implements Exception {}
