<?php
namespace Dayuse\Istorija\EventBus;

class TraceableEventBus implements EventBus
{
    /** @var EventBus */
    private $eventBus;

    /** @var Event[] */
    private $recordedEvents;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus       = $eventBus;
        $this->recordedEvents = [];
    }

    public function publish(Event $event): void
    {
        $this->recordedEvents[] = $event;

        $this->eventBus->publish($event);
    }

    public function publishAll(array $events): void
    {
        $this->recordedEvents = array_merge(
            $this->recordedEvents,
            $events
        );

        $this->eventBus->publishAll($events);
    }

    public function subscribe(string $eventType, callable $handler): void
    {
        $this->eventBus->subscribe($eventType, $handler);
    }

    public function getRecordedEvents(): array
    {
        return $this->recordedEvents;
    }

    public function resetRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }
}
