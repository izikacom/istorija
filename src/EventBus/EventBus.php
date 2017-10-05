<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 10:50
 */

namespace DayUse\Istorija\EventBus;


use DayUse\Istorija\SimpleMessaging\Bus;
use DayUse\Istorija\Utils\Ensure;

class EventBus
{
    /**
     * @var Bus
     */
    private $bus;

    /**
     * EventBus constructor.
     *
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param EventMessage[] $eventMessages
     */
    public function publishAll(array $eventMessages): void
    {
        Ensure::allIsInstanceOf($eventMessages, EventMessage::class);

        foreach ($eventMessages as $eventMessage) {
            $this->bus->publish($eventMessage);
        }

    }
}