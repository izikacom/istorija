<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 10:39
 */

namespace DayUse\Istorija\EventBus;


use DayUse\Istorija\SimpleMessaging\Message;

class EventMessage implements Message
{
    /**
     * @var EventMessage
     */
    private $eventEnvelope;

    /**
     * EventMessage constructor.
     *
     * @param $eventEnvelope
     */
    public function __construct(EventMessage $eventEnvelope)
    {
        $this->eventEnvelope = $eventEnvelope;
    }

    /**
     * @return EventMessage
     */
    public function getEventEnvelope(): EventMessage
    {
        return $this->eventEnvelope;
    }
}