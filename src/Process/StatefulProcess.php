<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\CommandBus\CommandBus;
use Dayuse\Istorija\Identifiers\Identifier;

abstract class StatefulProcess extends AbstractProcess
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var StateRepository
     */
    private $repository;

    public function __construct(CommandBus $commandBus, StateRepository $repository)
    {
        $this->commandBus = $commandBus;
        $this->repository = $repository;
    }

    protected function initState(Identifier $identifier, State $state): void
    {
        $processId = $this->getProcessId($identifier);

        $this->repository->save($processId, $state);
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
        $processId = $this->getProcessId($identifier);

        return $this->repository->find($processId);
    }

    public function closeState(Identifier $identifier): void
    {
        $processId = $this->getProcessId($identifier);
        $state     = $this->repository->find($processId);

        $this->repository->save($processId, $state->close());
    }

    public function handleCommand(Command $command): void
    {
        $this->commandBus->handle($command);
    }
}