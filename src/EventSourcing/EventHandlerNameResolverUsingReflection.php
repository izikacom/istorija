<?php


namespace Dayuse\Istorija\EventSourcing;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use ReflectionMethod;
use ReflectionObject;
use ReflectionParameter;

/**
 * Cette approche est meilleur que les EventNameGuesser.
 * Mais l'oppération de réflexion ne doit pas être fait au runtime.
 *
 * A mon avis; il faut supprimer les méthodes :
 * - AbstractEventHandler::supportEvent
 * - AbstractEventHandler::supportedEventClasses
 *
 * Ces deux méthodes doivent appartenir à un service externe qui permet d'analyse un EventHandler.
 *
 */
trait EventHandlerNameResolverUsingReflection
{
    private function methodNameResolver(DomainEvent $event): ?string
    {
        // legacy version
        // return self::HANDLER_PREFIX . EventNameGuesser::guess($event);

        $methodHandlers = $this->getMethodHandlers();

        return $methodHandlers[get_class($event)] ?? null;
    }

    private function getMethodHandlers(): array
    {
        // key : event_class
        // value : method name

        $eventHandlerReflection = new ReflectionObject($this);
        $eventHandlerMethods = $eventHandlerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $eventHandlerMethods = array_filter($eventHandlerMethods, static function (ReflectionMethod $reflectionMethod) {
            return 0 === strpos($reflectionMethod->getName(), AbstractEventHandler::HANDLER_PREFIX);
        });

        $methodHandlers = [];

        /** @var ReflectionMethod $eventHandlerMethod */
        foreach ($eventHandlerMethods as $eventHandlerMethod) {
            /** @var ReflectionParameter $parameter */
            $parameter = $eventHandlerMethod->getParameters()[0];

            $methodHandlers[$parameter->getClass()->getName()] = $eventHandlerMethod->getName();
        }

        return $methodHandlers;
    }
}