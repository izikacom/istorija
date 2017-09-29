<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 29/09/2017
 * Time: 10:30
 */

namespace DayUse\Istorija\EventSourcing\Testing;


use DayUse\Istorija\EventSourcing\AggregateRoot;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use DayUse\Istorija\EventSourcing\DomainEvent\DomainEventCollection;
use DayUse\Istorija\Utils\Ensure;

/**
 * given, when & then methods could be used more than once.
 *
 * Class Scenario
 *
 * @package DayUse\Istorija\EventSourcing\Testing
 */
class Scenario
{
    /**
     * @var AggregateRoot::class
     */
    private $aggregateRootClass;

    /**
     * @var AggregateRoot
     */
    private $aggregateRoot;

    /**
     * Scenario constructor.
     *
     * @param $aggregateRootClass
     */
    private function __construct($aggregateRootClass)
    {
        Ensure::string($aggregateRootClass);
        Ensure::classExists($aggregateRootClass);
        Ensure::subclassOf($aggregateRootClass, AggregateRoot::class);

        $this->aggregateRootClass = $aggregateRootClass;
    }

    static public function startFromClass($aggregateRootClass)
    {
        return new self($aggregateRootClass);
    }

    /**
     * The aggregateRoot instance passed as argument is used as the "given" events source.
     *
     * @param AggregateRoot $aggregateRoot
     *
     * @return Scenario
     */
    static public function startFromInstance(AggregateRoot $aggregateRoot)
    {
        $that   = new self(get_class($aggregateRoot));
        $events = $aggregateRoot->getRecordedEvents()->map(function (DomainEvent $event) use ($that) {
            return $event;
        });

        $that->given($events);

        return $that;
    }

    public function given(array $events)
    {
        Ensure::allIsInstanceOf($events, DomainEvent::class);

        if ($this->aggregateRoot) {
            foreach ($events as $event) {
                $this->aggregateRoot->apply($event);
            }
        } else {
            $this->aggregateRoot = $this->aggregateRootClass::reconstituteFromHistory(new DomainEventCollection($events));
        }

        return $this;
    }

    public function when(callable $when)
    {
        Ensure::isCallable($when);
        Ensure::notEmpty($this->aggregateRoot, 'Your aggregate was not instantiated; did you miss the given phase from this scenario?');

        $when($this->aggregateRoot);

        return $this;
    }

    public function then(array $allThen)
    {
        // $then could be
        // 1. a callable
        // 2. a classname
        // 3. a domain event instance
        Ensure::allSatisfy($allThen, function ($then) {
            if (is_callable($then)) {
                return true;
            }

            if (is_string($then) && class_exists($then) && is_subclass_of($then, DomainEvent::class)) {
                return true;
            }

            if ($then instanceof DomainEvent) {
                return true;
            }

            return false;
        });

        $recordedEvents = $this->aggregateRoot->getRecordedEvents();

        Ensure::eq(
            count($recordedEvents),
            count($allThen),
            sprintf('Scenario failed, expecting %s event(s), get %s', count($allThen), count($recordedEvents))
        );

        array_map(function ($idx, $then, DomainEvent $recordedEvent) {
            if (is_string($then)) {
                Ensure::isInstanceOf($recordedEvent, $then, sprintf(
                    '#%s expected event is not an instance of the given event class (%s)', $idx, $then
                ));

                return true;
            }

            if (is_callable($then)) {
                Ensure::satisfy($recordedEvent, $then, sprintf(
                    '#%s expected event does not satisfy the given callable', $idx
                ));

                return true;
            }

            // right now; $then is a instance of DomainEvent (see assertion of then())
            Ensure::eq($then, $recordedEvent, sprintf(
                '#%s expected event does not match the given event', $idx
            ));

            return true;
        }, array_keys($allThen), $allThen, iterator_to_array($recordedEvents));

        return $this;
    }
}