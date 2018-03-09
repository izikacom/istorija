<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\CommandBus\CommandBus;
use Dayuse\Istorija\Identifiers\Identifier;
use Dayuse\Istorija\Utils\State;

abstract class StatefulProcess extends AbstractProcess
{
    private $commandBus;
    private $repository;

    public function __construct(CommandBus $commandBus, StateRepository $repository)
    {
        $this->commandBus = $commandBus;
        $this->repository = $repository;
    }

    protected function initState(Identifier $identifier, State $state): void
    {
        $this->repository->save($this->getProcessId($identifier), $state);
    }

    public function setState(Identifier $identifier, callable $updateMethod): void
    {
        $processId    = $this->getProcessId($identifier);
        $currentState = $this->repository->find($processId);
        $nextState    = $updateMethod($currentState);

        $this->repository->save($processId, $nextState);
    }

    public function getState(Identifier $identifier): State
    {
        return $this->repository->find($this->getProcessId($identifier));
    }

    public function closeState(Identifier $identifier): void
    {
        $this->repository->close($this->getProcessId($identifier));
    }

    public function handleCommand(Command $command): void
    {
        $this->commandBus->handle($command);
    }
}
