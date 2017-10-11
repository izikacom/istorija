<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

use Bgy\TransientFaultHandling\ErrorDetectionStrategies\TransientErrorCatchAllStrategy;
use Bgy\TransientFaultHandling\RetryPolicy;
use Bgy\TransientFaultHandling\RetryStrategies\FixedInterval;

class ExecutionPipeline
{
    private $handlers = [];
    private $retryPolicy;

    public function __construct()
    {
        $this->retryPolicy = new RetryPolicy(new TransientErrorCatchAllStrategy(), new FixedInterval(
            3,
            1000000,
            false
        ));
    }

    public function addHandler(MessageHandler $messageHandler): void
    {
        $this->handlers[] = $messageHandler;
    }

    public function execute(Message $message, MessageHandlerContext $messageHandlerContext): void
    {
        foreach ($this->handlers as $handler) {
            $this->retryPolicy->execute(function () use ($handler, $message, $messageHandlerContext) {
                printf("Try to execute: %s\n", get_class($handler));
                call_user_func_array([$handler, 'handle'], [$message, $messageHandlerContext]);
            });
        }
    }
}
