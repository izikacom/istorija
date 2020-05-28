<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Messaging;

interface ExecutionPipeline
{
    public function addHandler(MessageHandler $messageHandler): void;

    public function execute(Message $message, MessageHandlerContext $messageHandlerContext): void;
}
