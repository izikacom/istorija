<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:10
 */

namespace DayUse\Istorija\EventBus;


use DayUse\Istorija\EventStore\EventEnvelope;
use DayUse\Istorija\Utils\Ensure;

class EventMessageFactory
{
    public function fromEventEnvelopes(array $eventEnvelopes)
    {
        Ensure::allIsInstanceOf($eventEnvelopes, EventEnvelope::class);

        return array_map(function(EventEnvelope $eventEnvelope) {
            return new EventMessage($eventEnvelope);
        }, $eventEnvelopes);
    }
}