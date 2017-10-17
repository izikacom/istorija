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

    abstract public function init(): void;

    abstract public function reset(): void;

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
    final public function apply(DomainEvent $event): void
    {
        $method = self::HANDLER_PREFIX . EventNameGuesser::guess($event);
        if (is_callable([$this, $method])) {
            call_user_func([$this, $method], $event);
        }
    }
}