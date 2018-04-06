<?php
namespace Dayuse\Istorija\EventBus;

use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\SendOptions;
use Dayuse\Istorija\Messaging\Subscription;
use Dayuse\Istorija\Messaging\Transport\MessageHandlerCallable;
use Dayuse\Istorija\Utils\Ensure;

interface EventBus
{
    public function publish(Event $event): void;

    public function publishAll(array $events): void;

    public function subscribe(string $eventType, callable $handler): void;
}
