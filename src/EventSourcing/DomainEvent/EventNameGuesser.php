<?php

namespace Dayuse\Istorija\EventSourcing\DomainEvent;

class EventNameGuesser
{
    public static function guess(DomainEvent $event): string
    {
        $class = trim(get_class($event), "\\");

        if (strpos($class, "\\") === false) {
            return $class;
        }

        $parts = explode("\\", $class);

        return end($parts);
    }
}
