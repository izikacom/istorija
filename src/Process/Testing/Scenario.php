<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process\Testing;

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\CommandBus\TraceableCommandBus;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\Process\Process;
use Dayuse\Istorija\Utils\Ensure;

class Scenario
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var TraceableCommandBus
     */
    private $commandBus;

    public function __construct(Process $process, TraceableCommandBus $commandBus)
    {
        $this->process    = $process;
        $this->commandBus = $commandBus;
    }

    public function given(array $events)
    {
        Ensure::allIsInstanceOf($events, DomainEvent::class);

        foreach ($events as $event) {
            $this->process->apply($event);
        }

        $this->commandBus->resetRecordedCommands();

        return $this;
    }

    public function when($when)
    {
        // $when
        // 1. callable - inject process to the callable
        // 2. event - apply to the process

        if (is_callable($when)) {
            call_user_func($when($this->process));
        } elseif ($when instanceof DomainEvent) {
            $this->process->apply($when);
        } else {
            throw new \InvalidArgumentException('Do not support this kind of when...');
        }

        return $this;
    }

    public function then(array $allThen = [])
    {
        // $then could be
        // 1. a callable
        // 2. a classname
        // 3. a command instance
        Ensure::allSatisfy($allThen, function ($then) {
            if (is_callable($then)) {
                return true;
            }

            if (is_string($then) && class_exists($then) && is_subclass_of($then, Command::class)) {
                return true;
            }

            if ($then instanceof Command) {
                return true;
            }

            return false;
        });

        $recordedCommands = $this->commandBus->getRecordedCommands();

        Ensure::eq(
            count($recordedCommands),
            count($allThen),
            sprintf('Scenario failed, expecting %s command(s), get %s', count($allThen), count($recordedCommands))
        );

        array_map(function ($idx, $then, Command $recordedCommand) {
            if (is_string($then)) {
                Ensure::isInstanceOf($recordedCommand, $then, sprintf(
                    '#%s expected command is not an instance of the asserted command class (%s)',
                    $idx,
                    $then
                ));

                return true;
            }

            if (is_callable($then)) {
                Ensure::satisfy($recordedCommand, $then, sprintf(
                    '#%s expected command does not satisfy the callable algorithm',
                    $idx
                ));

                return true;
            }

            // right now; $then is a instance of DomainEvent (see assertion of then())
            Ensure::eq($then, $recordedCommand, sprintf(
                '#%s expected command does not match the asserted command',
                $idx
            ));

            return true;
        }, array_keys($allThen), $allThen, $recordedCommands);

        return $this;
    }
}
