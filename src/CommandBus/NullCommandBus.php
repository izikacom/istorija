<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\CommandBus;


class NullCommandBus implements CommandBus
{

    public function register(string $commandType, callable $callable): void
    {

    }

    public function handle(Command $command): void
    {

    }
}