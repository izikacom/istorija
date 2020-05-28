<?php

namespace Dayuse\Istorija\Messaging;


use Psr\Log\LoggerInterface;

class ExecutionPipelineFactory
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createExecutionPipeline(array $messageHandlers = []): ExecutionPipeline
    {
        return new ExecutionPipeline($this->logger, $messageHandlers);
    }
}