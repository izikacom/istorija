<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\CommandBus;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Verraes\ClassFunctions\ClassFunctions;

class LoggerCommandBus implements CommandBus
{
    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger = null)
    {
        $this->commandBus = $commandBus;
        $this->logger     = $logger ?? new NullLogger();
    }

    /**
     * @inheritdoc
     */
    public function register(string $commandType, callable $callable): void
    {
        $this->commandBus->register($commandType, $callable);

        $this->logger->info('Command handler have been registered', [
            'type'    => $commandType,
            'handler' => $callable,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function handle(Command $command): void
    {
        $identifier = (string)Uuid::uuid4();
        $type       = ClassFunctions::fqcn($command);

        $this->logger->debug('Command will be handled', [
            'uuid'    => $identifier,
            'type'    => $type,
            'command' => $command,
        ]);

        try {
            $this->commandBus->handle($command);

            $this->logger->info('Command have been handled', [
                'uuid'    => $identifier,
                'type'    => $type,
                'command' => $command,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Command could not be handled due to exception', [
                'uuid'      => $identifier,
                'type'      => $type,
                'command'   => $command,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
