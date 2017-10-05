<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:29
 */

namespace DayUse\Istorija\CommandBus;


use DayUse\Istorija\SimpleMessaging\Bus;

class CommandBus
{
    /**
     * @var Bus
     */
    private $bus;

    public function register(string $commandType, callable $callable)
    {
        $this->bus->subscribe($commandType, $callable);
    }

    public function handle(Command $command)
    {
        $this->bus->publish($command);
    }
}