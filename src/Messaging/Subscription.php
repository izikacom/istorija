<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

class Subscription
{
    private $messageContract;
    private $handler;

    public function __construct(string $messageContract, MessageHandler $handler)
    {
        $this->messageContract = $messageContract;
        $this->handler = $handler;
    }

    public function getMessageContract(): string
    {
        return $this->messageContract;
    }

    public function getHandler(): MessageHandler
    {
        return $this->handler;
    }
}
