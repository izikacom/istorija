<?php


namespace Dayuse\Istorija\EventSourcing;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use ReflectionMethod;
use ReflectionObject;
use ReflectionParameter;

trait EventHandlerNameResolverUsingNameGuesser
{
    public function supportedEventClasses(): array
    {
        $eventHandlerReflection = new ReflectionObject($this);
        $eventHandlerMethods = $eventHandlerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $eventHandlerMethods = array_filter($eventHandlerMethods, static function (ReflectionMethod $reflectionMethod) {
            return 0 === strpos($reflectionMethod->getName(), AbstractEventHandler::HANDLER_PREFIX);
        });

        $eventClasses = [];

        /** @var ReflectionMethod $eventHandlerMethod */
        foreach ($eventHandlerMethods as $eventHandlerMethod) {
            /** @var ReflectionParameter $parameter */
            $parameter = $eventHandlerMethod->getParameters()[0];

            $eventClasses[] = $parameter->getClass()->getName();
        }

        return array_values(array_unique($eventClasses));
    }

    private function methodNameResolver(DomainEvent $event): string
    {
        return AbstractEventHandler::HANDLER_PREFIX . EventNameGuesser::guess($event);
    }
}