<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 23/09/2016
 * Time: 16:07
 */

namespace DayUse\Istorija\EventSourcing;

use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventRecorder;
use DayUse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;
use DayUse\Istorija\Identifiers\Identifier;

trait EventSourcedObject
{
    /**
     * @var DomainEventRecorder
     */
    private $domainEventRecorder;

    /**
     * @var Entity[]
     */
    private $entities = [];

    /**
     * @return Identifier
     */
    abstract public function getId();

    /**
     * @param DomainEvent $event
     *
     * @return boolean
     */
    abstract public function isEventCanBeApplied(DomainEvent $event);

    public static function reconstituteFromSingleEvent(DomainEvent $event)
    {
        /** @var AggregateRoot $instance */
        $instance = new static();
        $instance->configureEventRecorder();
        $instance->apply($event);

        return $instance;
    }

    /**
     * Reconstitute the AggregateRoot state from its event history
     *
     * @param DomainEventCollection $history
     *
     * @return AggregateRoot
     */
    public static function reconstituteFromHistory(DomainEventCollection $history)
    {
        /** @var AggregateRoot $instance */
        $instance = new static();
        $instance->configureEventRecorder();

        foreach ($history as $event) {
            $instance->apply($event);
        }

        return $instance;
    }

    /**
     * Ask execution of the given Domain Event behavior
     * and add the event to the recorded events to be committed.
     *
     * @param DomainEvent $event
     */
    protected function recordThat(DomainEvent $event)
    {
        if (null === $this->domainEventRecorder) {
            $this->configureEventRecorder();
        }

        $this->domainEventRecorder->recordThat($event);
    }

    protected function configureEventRecorder()
    {
        // Just after recording, event have to be applied from the AggregateRoot to related entities.
        $this->domainEventRecorder = new DomainEventRecorder(function (DomainEvent $event) {
            $this->apply($event);
        });
    }

    /**
     *
     * @param Entity $entity
     *
     * @return Entity
     */
    protected function captureEntity(Entity $entity)
    {
        $entity->changeEventRecorder($this->domainEventRecorder, true);

        return $this->getEntity($entity->getId());
    }

    protected function registerEntity($entityClass, DomainEvent $firstEvent)
    {
        if (false === is_subclass_of($entityClass, Entity::class)) {
            throw new \InvalidArgumentException('First argument should be an entity');
        }

        /** @var Entity $entityClass */
        /** @var Entity $entity */
        $entity = $entityClass::reconstituteFromSingleEvent($firstEvent);

        $id = (string)$entity->getId();
        if (array_key_exists($id, $this->entities)) {
            throw new \RuntimeException('Entity was already registered');
        }

        $entity->changeEventRecorder($this->domainEventRecorder, false);

        $this->entities[$id] = $entity;

        return $entity;
    }

    protected function releaseEntity(Identifier $id, DomainEvent $latestEvent)
    {
        $id = (string)$id;

        if (false === array_key_exists($id, $this->entities)) {
            throw new \RuntimeException('Try to release an entity that was never registered. Did you miss the entity registration from parent? See apply{EventEntityCreated} ?');
        }

        $this->entities[$id]->apply($latestEvent);

        unset($this->entities[$id]);
    }

    protected function getEntity(Identifier $id)
    {
        $id = (string)$id;
        if (false === array_key_exists($id, $this->entities)) {
            throw new \RuntimeException('Try to release an entity that was never registered. Did you miss the entity registration from parent? See apply{EventEntityCreated} ?');
        }

        return $this->entities[$id];
    }

    /**
     * Tries to call the apply* behavioral method corresponding the given event.
     *
     * From current instance to related entities.
     *
     * The function is called from the AggregateRoot first.
     *
     * @param DomainEvent $event
     */
    public function apply(DomainEvent $event)
    {
        $method = 'apply' . EventNameGuesser::guess($event);
        if (is_callable([$this, $method])) {
            $this->{$method}($event);
        }

        $this->applyToEntities($event);
    }

    /**
     * Tries to call the apply* behavioral method corresponding
     * the given event.
     *
     * @param DomainEvent $event
     */
    protected function applyToEntities(DomainEvent $event)
    {
        if (empty($this->entities)) {
            return;
        }

        /** @var Entity $entity */
        foreach ($this->entities as $entity) {
            if (false === $entity->isEventCanBeApplied($event)) {
                continue;
            }

            $entity->apply($event);
        }
    }
}