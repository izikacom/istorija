<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 17:15
 */

namespace DayUse\Istorija\Projection;

use DayUse\Istorija\DAO\DAOInterface;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use DayUse\Istorija\EventStore\EventMetadata;

abstract class PersistedPartitionedProjector implements Projection
{
    const HANDLER_PREFIX = "when";

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
     * @param DomainEvent   $event
     * @param EventMetadata $metadata
     */
    final public function apply(DomainEvent $event, EventMetadata $metadata)
    {
        $method = self::HANDLER_PREFIX . EventNameGuesser::guess($event);
        if (is_callable([$this, $method])) {
            $partition    = $this->findPartitionFromEvent($event, $metadata);
            $updatedState = $this->{$method}(
                $this->getDAO()->find($partition),
                $event,
                $metadata
            );

            $this->getDAO()->update($partition, $updatedState);
        }
    }

    final public function reset(): self
    {
        $this->getDAO()->flush();

        return $this;
    }

    abstract protected function findPartitionFromEvent(DomainEvent $event, EventMetadata $metadata);

    /**
     * @return DAOInterface
     */
    abstract protected function getDAO(): DAOInterface;
}