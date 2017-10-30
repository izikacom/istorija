<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:29
 */

namespace Dayuse\Istorija\CommandBus;


use Dayuse\Istorija\Messaging\Bus;
use Dayuse\Istorija\Messaging\SendOptions;
use Dayuse\Istorija\Messaging\Subscription;
use Dayuse\Istorija\Messaging\Transport\MessageHandlerCallable;

interface CommandBus
{
    public function register(string $commandType, callable $callable) : void;
    public function handle(Command $command) : void;
}