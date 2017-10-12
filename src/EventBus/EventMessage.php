<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 10:39
 */

namespace DayUse\Istorija\EventBus;


use DayUse\Istorija\EventStore\EventEnvelope;
use DayUse\Istorija\Messaging\Message;

class EventMessage implements Message
{
    /**
     * @var EventEnvelope
     */
    private $eventEnvelope;

    /**
     * EventEnvelope constructor.
     *
     * @param $eventEnvelope
     */
    public function __construct(EventEnvelope $eventEnvelope)
    {
        $this->eventEnvelope = $eventEnvelope;
    }

    /**
     * @return EventEnvelope
     */
    public function getEventEnvelope(): EventEnvelope
    {
        return $this->eventEnvelope;
    }
}