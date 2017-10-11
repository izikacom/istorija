<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

interface MessageHandler
{
    public function handle(Message $message, MessageHandlerContext $context): void;
}
