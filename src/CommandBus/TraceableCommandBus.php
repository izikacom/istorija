<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\CommandBus;

class TraceableCommandBus implements CommandBus
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var Command[]
     */
    private $recordedCommands;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus       = $commandBus;
        $this->recordedCommands = [];
    }

    public function register(string $commandType, callable $callable): void
    {
        $this->commandBus->register($commandType, $callable);
    }

    /**
     * @inheritdoc
     */
    public function handle(Command $command): void
    {
        $this->recordedCommands[] = $command;

        $this->commandBus->handle($command);
    }

    public function getRecordedCommands(): array
    {
        return $this->recordedCommands;
    }

    public function resetRecordedCommands(): void
    {
        $this->recordedCommands = [];
    }
}
