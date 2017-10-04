<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 16:17
 */

namespace DayUse\Istorija\Projection;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use DayUse\Istorija\EventStore\EventMetadata;
use DayUse\Istorija\Utils\Ensure;

/**
 * Class Query
 *
 * An event store query reads one or multiple event stream, aggregates some state from it and makes it accessible.
 * A query is non-persistent, will only get executed once, return a result, and that's it.
 *
 * @package DayUse\Istorija\Projection
 */
final class Query implements Projection
{
    /**
     * @var callable
     */
    private $initializationCallback;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @var mixed
     */
    private $state;

    public function init(callable $callback): self
    {
        Ensure::null($this->initializationCallback, 'Query was already initialized');

        $this->initializationCallback = $callback;

        $this->state = call_user_func($this->initializationCallback);

        return $this;
    }

    /**
     * For example:
     *
     * when([
     *     'UserCreated' => function (array $state, DomainEvent $event) {
     *         $state['count']++;
     *         return $state;
     *     },
     *     'UserDeleted' => function (array $state, DomainEvent $event) {
     *         $state['count']--;
     *         return $state;
     *     }
     * ])
     *
     * @param array $handlers
     *
     * @return self
     */
    public function when(array $handlers)
    {
        Ensure::noContent($this->handlers, 'Handlers were already configured.');
        Ensure::allString(array_keys($handlers));
        Ensure::allIsCallable(array_values($handlers));

        $this->handlers = $handlers;

        return $this;
    }

    final public function apply(DomainEvent $event, EventMetadata $metadata)
    {
        $eventName = get_class($event);
        $handler   = $this->handlers[$eventName] ?? null;

        if (null === $handler) {
            return;
        }

        $this->state = call_user_func($handler, $this->state, $event, $metadata);
    }

    public function reset()
    {
        Ensure::notNull($this->initializationCallback, 'Did you forget to initialize this query?');

        $this->state = call_user_func($this->initializationCallback);

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }
}