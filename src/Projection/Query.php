<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 16:17
 */

namespace Dayuse\Istorija\Projection;

use Dayuse\Istorija\EventSourcing\AbstractEventHandler;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\Utils\Ensure;

/**
 * Class Query
 *
 * An event store query reads one or multiple event stream, aggregates some state from it and makes it accessible.
 * A query is non-persistent, will only get executed once, return a result, and that's it.
 *
 * @package Dayuse\Istorija\Projection
 */
final class Query extends AbstractEventHandler implements Projection
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
     * @var State
     */
    private $state;

    public function init(callable $callback): void
    {
        Ensure::null($this->initializationCallback, 'Query was already initialized');

        $this->initializationCallback = $callback;

        $this->state = $this->initializationCallback();
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
     */
    public function when(array $handlers): void
    {
        Ensure::noContent($this->handlers, 'Handlers were already configured.');
        Ensure::allString(array_keys($handlers));
        Ensure::allIsCallable(array_values($handlers));

        $this->handlers = $handlers;
    }

    public function apply(DomainEvent $event): void
    {
        $eventName = \get_class($event);
        $handler   = $this->handlers[$eventName] ?? null;

        if (null === $handler) {
            return;
        }

        $this->state = $handler($this->state, $event);
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