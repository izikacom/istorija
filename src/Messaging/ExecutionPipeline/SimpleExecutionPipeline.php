<?php

namespace Dayuse\Istorija\Messaging\ExecutionPipeline;


use Dayuse\Istorija\Messaging\ExecutionPipeline;
use Dayuse\Istorija\Messaging\Message;
use Dayuse\Istorija\Messaging\MessageHandler;
use Dayuse\Istorija\Messaging\MessageHandlerContext;

class SimpleExecutionPipeline implements ExecutionPipeline
{
    private $handlers = [];

    public function addHandler(MessageHandler $messageHandler): void
    {
        $this->handlers[] = $messageHandler;
    }

    public function execute(Message $message, MessageHandlerContext $messageHandlerContext): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($message, $messageHandlerContext);
        }
    }

    public static function creator(): callable
    {
        return static function() {
            return new self();
        };
    }
}