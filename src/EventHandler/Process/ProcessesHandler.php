<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\EventHandler\Process;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\Utils\Ensure;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ProcessesHandler
{
    /**
     * @var Process[]
     */
    private $processes = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(array $processes, LoggerInterface $logger = null)
    {
        Ensure::allIsInstanceOf($processes, Process::class);

        $this->processes = $processes;
        $this->logger    = $logger ?? new NullLogger();
    }


    public function handle(DomainEvent $event): void
    {
        foreach ($this->processes as $process) {
            if (false === $process->supportEvent($event)) {
                continue;
            }

            try {
                $process->apply($event);

                $this->logger->info('Event have been processed.', [
                    'event'   => $event,
                    'process' => $process->getName(),
                ]);
            } catch (\Throwable $e) {
                $this->logger->error('An error occurred when processing an event.', [
                    'exception' => $e,
                    'event'     => $event,
                    'process'   => $process->getName(),
                ]);
            }
        }
    }
}
