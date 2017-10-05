<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\SimpleMessaging;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Bus
{
    private $subscribers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Bus constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->subscribers = [];
        $this->logger      = $logger ?? new NullLogger();
    }


    public function publish(Message $message): void
    {
        foreach ($this->subscribers as $messageType => $callables) {
            if ($messageType === get_class($message)) {
                foreach ($callables as $callable) {
                    try {
                        call_user_func_array($callable, [$message]);
                    } catch (\Throwable $e) {
                        $this->logger->warning(sprintf('An error occurred when publishing event message.'), [
                            'exception'   => $e,
                            'messageType' => $messageType,
                        ]);
                    };
                }
            }
        }
    }

    public function subscribe(string $messageType, callable $callable): void
    {
        $this->subscribers[$messageType][] = $callable;
    }
}
