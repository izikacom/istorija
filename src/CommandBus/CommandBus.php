<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:29
 */

namespace DayUse\Istorija\CommandBus;


use DayUse\Istorija\Messaging\Bus;
use DayUse\Istorija\Messaging\Subscription;
use DayUse\Istorija\Messaging\Transport\MessageHandlerCallable;

class CommandBus
{
    /**
     * @var Bus
     */
    private $bus;

    /**
     * CommandBus constructor.
     *
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function register(string $commandType, callable $callable)
    {
        $this->bus->subscribe(new Subscription($commandType, new MessageHandlerCallable($callable)));
    }

    public function handle(Command $command)
    {
        $this->bus->send($command);
    }
}