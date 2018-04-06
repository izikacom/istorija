<?php
namespace Dayuse\Istorija\EventBus;

interface EventBus
{
    public function publish(Event $event): void;

    public function publishAll(array $events): void;

    public function subscribe(string $eventType, callable $handler): void;
}
