<?php
namespace Dayuse\Istorija\EventBus;

use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\SendOptions;
use Dayuse\Istorija\Messaging\Subscription;
use Dayuse\Istorija\Messaging\Transport\MessageHandlerCallable;
use Dayuse\Istorija\Utils\Ensure;

class GenericEventBus implements EventBus
{
    /** @var Bus */
    private $bus;

    /** @var Event[]  */
    private $queue = [];

    /** @var bool */
    private $isPublishing = false;

    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function publish(Event $event): void
    {
        $this->queue[] = $event;

        if (!$this->isPublishing) {
            $this->isPublishing = true;

            try {
                while ($event = array_shift($this->queue)) {
                    $this->bus->send($event, (new SendOptions())->sendLocal());
                }
            } finally {
                $this->isPublishing = false;
            }
        }
    }

    /**
     * @param Event[] $events
     */
    public function publishAll(array $events): void
    {
        Ensure::allIsInstanceOf($events, Event::class);

        foreach ($events as $event) {
            $this->publish($event);
        }
    }

    public function subscribe(string $eventType, callable $handler): void
    {
        $this->bus->subscribe(new Subscription($eventType, new MessageHandlerCallable($handler)));
    }
}
