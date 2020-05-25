<?php

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use InvalidArgumentException;
use ReflectionMethod;
use ReflectionObject;
use ReflectionParameter;
use function is_callable;

class AbstractEventHandler implements EventHandler
{
    private const HANDLER_PREFIX = 'when';

    public function apply(DomainEvent $event): void
    {
        $method = $this->methodNameResolver($event);

        if (!$this->supportEvent($event)) {
            throw new InvalidArgumentException('Event handler does not support event');
        }

        $this->$method($event);
    }

    public function supportEvent(DomainEvent $event): bool
    {
        $method = $this->methodNameResolver($event);

        return is_callable([$this, $method]);
    }

    public function supportedEventClasses(): array
    {
        $eventHandlerReflection = new ReflectionObject($this);
        $eventHandlerMethods = $eventHandlerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $eventHandlerMethods = array_filter($eventHandlerMethods, static function (ReflectionMethod $reflectionMethod) {
            return 0 === strpos($reflectionMethod->getName(), self::HANDLER_PREFIX);
        });

        $eventClasses = [];

        /** @var ReflectionMethod $eventHandlerMethod */
        foreach ($eventHandlerMethods as $eventHandlerMethod) {
            /** @var ReflectionParameter $parameter */
            $parameter = $eventHandlerMethod->getParameters()[0];

            $eventClasses[] = $parameter->getClass()->getName();
        }

        return array_Values(array_unique($eventClasses));
    }

    private function methodNameResolver(DomainEvent $event): string
    {
        return self::HANDLER_PREFIX . EventNameGuesser::guess($event);
    }
}
