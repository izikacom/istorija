<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\SimpleMessaging;

class Bus
{
    private $subscribers = [];

    public function publish(Message $message): void
    {
        foreach ($this->subscribers as $messageType => $callables) {
            if ($messageType === get_class($message)) {
                foreach ($callables as $callable) {
                    call_user_func_array($callable, [$message]);
                }
            }
        }
    }

    public function subscribe(string $messageType, callable $callable): void
    {
        $this->subscribers[$messageType][] = $callable;
    }
}
