<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 16:18
 */

namespace DayUse\Istorija\Projection;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;

abstract class Projector implements Projection
{
    const HANDLER_PREFIX = "when";

    /**
     * @var mixed
     */
    private $state;

    abstract public function initialState();

    /**
     * This method is trying to apply received events on when{EventName} methods.
     * Called method arguments will be:
     * 1. event
     * 2. previous state
     *
     * returned state will be used as current state;
     *
     * ie:
     * whenUserPaid($state, UserPaid $event) {
     *   $current = [
     *     'paid' => true,
     *   ];
     *
     *   return array_merge($state, $current);
     * }
     *
     *
     * @param DomainEvent $event
     */
    final public function apply(DomainEvent $event)
    {
        $method = self::HANDLER_PREFIX . EventNameGuesser::guess($event);
        if (is_callable([$this, $method])) {
            $updatedState = $this->{$method}(
                $this->getState(),
                $event
            );

            $this->updateState($updatedState);
        }
    }

    final public function reset(): self
    {
        $this->updateState($this->initialState());

        return $this;
    }

    /**
     * @param mixed $state
     *
     * @return mixed
     */
    protected function updateState($state)
    {
        $this->state = $state;
    }

    /**
     * @return array
     */
    public function getState()
    {
        return $this->state;
    }
}