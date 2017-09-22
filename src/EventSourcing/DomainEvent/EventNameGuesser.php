<?php

namespace DayUse\Istorija\EventSourcing\DomainEvent;

class EventNameGuesser
{
    /**
     * @param DomainEvent $event
     * @return string
     */
    public static function guess(DomainEvent $event)
    {
        $class = trim(get_class($event), "\\");

        if (strpos($class, "\\") === false) {
            return $class;
        }

        $parts = explode("\\", $class);

        return end($parts);
    }
}
