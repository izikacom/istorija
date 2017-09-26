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
use DayUse\Istorija\EventStore\EventMetadata;

abstract class Projector implements EventHandler
{
    const HANDLER_PREFIX = "when";

    /**
     * @var mixed
     */
    private $state;

    abstract public function initialization();

    /**
     * This method is trying to apply received events on when{EventName} methods.
     * Called method arguments will be:
     * 1. event
     * 2. previous state
     *
     * returned state will be used as current state;
     *
     * ie:
     * whenUserPaid(UserPaid $event, $previous) {
     *   $current = [
     *     'paid' => true,
     *   ];
     *
     *   return array_merge($previous, $current);
     * }
     *
     *
     * @param DomainEvent   $event
     * @param EventMetadata $metadata
     */
    final public function apply(DomainEvent $event, EventMetadata $metadata)
    {
        $method = self::HANDLER_PREFIX . EventNameGuesser::guess($event);
        if (is_callable([$this, $method])) {
            $this->state = $this->{$method}($event, $this->state);
        }
    }

    public function reset(): self
    {
        $this->state = $this->initialization();

        return $this;
    }

    /**
     * @return array
     */
    public function getState()
    {
        return $this->state;
    }
}