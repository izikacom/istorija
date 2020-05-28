<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging;

use Dayuse\Istorija\Utils\Ensure;
use Psr\Log\LoggerInterface;
use Throwable;

class ExecutionPipeline
{
    private $logger;
    private $handlers;

    public function __construct(LoggerInterface $logger, array $handlers)
    {
        Ensure::allIsInstanceOf($handlers, MessageHandler::class);

        $this->logger = $logger;
        $this->handlers = $handlers;
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
}
