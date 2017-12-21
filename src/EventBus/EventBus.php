<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 10:50
 */

namespace Dayuse\Istorija\EventBus;

use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\SendOptions;
use Dayuse\Istorija\Messaging\Subscription;
use Dayuse\Istorija\Messaging\Transport\MessageHandlerCallable;
use Dayuse\Istorija\Utils\Ensure;

class EventBus
{
    /**
     * @var Bus
     */
    private $bus;

    /**
     * EventBus constructor.
     *
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param Event[] $events
     */
    public function publishAll(array $events): void
    {
        Ensure::allIsInstanceOf($events, Event::class);

        foreach ($events as $event) {
            $this->bus->send($event, (new SendOptions())->sendLocal());
        }
    }

    public function subscribe(string $eventType, callable $handler): void
    {
        $this->bus->subscribe(new Subscription($eventType, new MessageHandlerCallable($handler)));
    }
}
