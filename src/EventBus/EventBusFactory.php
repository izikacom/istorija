<?php

namespace Dayuse\Istorija\EventBus;


use Dayuse\Istorija\EventSourcing\EventHandler;
use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\Configuration;
use Dayuse\Istorija\Messaging\ExecutionPipeline\FaultToleranceExecutionPipeline;
use Dayuse\Istorija\Messaging\ExecutionPipeline\SimpleExecutionPipeline;
use Dayuse\Istorija\Messaging\Settings;
use Dayuse\Istorija\Utils\Ensure;
use Dayuse\Istorija\Utils\ExecutionContext;
use Psr\Log\LoggerInterface;

class EventBusFactory
{
    private $applicationExecutionContext;
    private $logger;
    private $eventHandlers;
    private $eventHandlerSorter;

    public function __construct(
        ExecutionContext $applicationExecutionContext,
        LoggerInterface $logger,
        iterable $eventHandlers,
        EventHandlerSorter $eventHandlerSorter
    ) {
        Ensure::allIsInstanceOf($eventHandlers, EventHandler::class);

        $this->applicationExecutionContext = $applicationExecutionContext;
        $this->logger = $logger;
        $this->eventHandlers = $eventHandlers;
        $this->eventHandlerSorter = $eventHandlerSorter;
    }

    public function createForApplication(): EventBus
    {
        return $this->configureForEventHandlers(
            new TraceableEventBus(new GenericEventBus(
                new Bus(
                    new Configuration(new Settings()),
                    $this->applicationExecutionContext,
                    FaultToleranceExecutionPipeline::creator($this->logger)
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
                    new ExecutionContext(),
                    SimpleExecutionPipeline::creator()
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
            // TODO - Utiliser un service externe pour savoir quelles sont les évènements supportés par un EventHandler, ainsi que les méthodes de réception.
            // Voir le commande dans EventHandlerNameResolverUsingReflection.
            foreach ($eventHandler->supportedEventClasses() as $eventClass) {
                $eventBus->subscribe($eventClass, [$eventHandler, 'apply']);
            }
        }

        return $eventBus;
    }
}