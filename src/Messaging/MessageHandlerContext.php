<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Messaging;

use DayUse\Istorija\Messaging\Transport\Headers;
use DayUse\Istorija\Utils\ExecutionContext as GlobalExecutionContext;

class MessageHandlerContext
{
    private $bus;
    private $messageHeaders;
    private $executionContext;

    public function __construct(Bus $bus, Headers $messageHeaders, GlobalExecutionContext $executionContext)
    {
        $this->bus = $bus;
        $this->messageHeaders = $messageHeaders;
        $this->executionContext = $executionContext;
    }

    public function send(Message $message, ?SendOptions $options = null)
    {
        $this->bus->send($message, $options);
    }

    public function getMessageHeaders(): Headers
    {
        return $this->messageHeaders;
    }

    public function getExecutionContext(): GlobalExecutionContext
    {
        return $this->executionContext;
    }
}
