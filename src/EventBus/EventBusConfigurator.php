<?php

namespace Dayuse\Istorija\EventBus;


use Dayuse\Istorija\EventSourcing\EventHandler;

class EventBusConfigurator
{
    public function configureWithEventHandler(EventBus $eventBus, EventHandler $eventHandler): void
    {
        foreach ($eventHandler->supportedEventClasses() as $eventClass) {
            $eventBus->subscribe($eventClass, [$eventHandler, 'apply']);
        }
    }
}