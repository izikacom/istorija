<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 12/10/2017
 * Time: 14:18
 */

namespace DayUse\Istorija\Messaging\Transport;


use DayUse\Istorija\Messaging\Message;
use DayUse\Istorija\Messaging\MessageHandler;
use DayUse\Istorija\Messaging\MessageHandlerContext;

class MessageHandlerCallable implements MessageHandler
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * MessageHandlerCallable constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function handle(Message $message, MessageHandlerContext $context): void
    {
        call_user_func($this->callable, $message, $context);
    }
}