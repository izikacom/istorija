<?php

namespace Dayuse\Istorija\Messaging\ExecutionPipeline;


use Dayuse\Istorija\Messaging\ExecutionPipeline;
use Dayuse\Istorija\Messaging\Message;
use Dayuse\Istorija\Messaging\MessageHandler;
use Dayuse\Istorija\Messaging\MessageHandlerContext;
use Psr\Log\LoggerInterface;
use Throwable;

class FaultToleranceExecutionPipeline implements ExecutionPipeline
{
    private $logger;
    private $handlers;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->handlers = [];
    }

    public function addHandler(MessageHandler $messageHandler): void
    {
        $this->handlers[] = $messageHandler;
    }

    public function execute(Message $message, MessageHandlerContext $messageHandlerContext): void
    {
        foreach ($this->handlers as $handler) {
            try {
                $handler->handle($message, $messageHandlerContext);
            } catch (Throwable $exception) {
                $this->logger->error('An exception occurred when handling message into bus', [
                    'exception' => $exception,
                    'handler' => get_class($handler),
                    'message' => get_class($message),
                ]);
            }
        }
    }

    public static function creator(LoggerInterface $logger): callable
    {
        return static function () use ($logger) {
            return new self($logger);
        };
    }
}