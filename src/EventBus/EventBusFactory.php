<?php

namespace Dayuse\Istorija\EventBus;


use Dayuse\Istorija\EventSourcing\EventHandler;
use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\Configuration;
use Dayuse\Istorija\Messaging\Settings;
use Dayuse\Istorija\Utils\Ensure;
use Dayuse\Istorija\Utils\ExecutionContext;

class EventBusFactory
{
    private $applicationExecutionContext;
    private $eventHandlers;
    private $eventHandlerSorter;

    public function __construct(
        ExecutionContext $applicationExecutionContext,
        iterable $eventHandlers,
        EventHandlerSorter $eventHandlerSorter
    ) {
        Ensure::allIsInstanceOf($eventHandlers, EventHandler::class);

        $this->applicationExecutionContext = $applicationExecutionContext;
        $this->eventHandlers = $eventHandlers;
        $this->eventHandlerSorter = $eventHandlerSorter;
    }

    public function createForApplication(): EventBus
    {
        return $this->configureForEventHandlers(
            new TraceableEventBus(new GenericEventBus(
                new Bus(
                    new Configuration(new Settings()),
                    $this->applicationExecutionContext
                )
            )),
            $this->eventHandlers
        );
    }

    public function createForReplay(iterable $eventHandlers): EventBus
    {
        Ensure::allIsInstanceOf($eventHandlers, EventHandler::class);

        return $this->configureForEventHandlers(
            new GenericEventBus(
                new Bus(
                    new Configuration(new Settings()),
                    new ExecutionContext()
                )
            ),
            $eventHandlers
        );
    }

    private function configureForEventHandlers(EventBus $eventBus, iterable $eventHandlers): EventBus
    {
        // Certains handlers sont prioritaires.
        $eventHandlers = $this->eventHandlerSorter->sort($eventHandlers);

        foreach ($eventHandlers as $eventHandler) {
            foreach ($eventHandler->supportedEventClasses() as $eventClass) {
                $eventBus->subscribe($eventClass, [$eventHandler, 'apply']);
            }
        }

        return $eventBus;
    }
}