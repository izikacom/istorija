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

class GenericCommandBus implements CommandBus
{
    /** @var Bus */
    private $bus;

    /** @var Command[]  */
    private $queue = [];

    /** @var bool */
    private $isHandling = false;

    /**
     * CommandBus constructor.
     *
     * @param Bus $bus
     */
    public function __construct(Bus $bus)
    {
        $this->bus = $bus;
    }

    public function register(string $commandType, callable $callable) : void
    {
        $this->bus->subscribe(new Subscription($commandType, new MessageHandlerCallable($callable)));
    }

    /**
     * @inheritdoc
     */
    public function handle(Command $command) : void
    {
        $this->queue[] = $command;


        if (!$this->isHandling) {
            $this->isHandling = true;

            try {
                while ($command = array_shift($this->queue)) {
                    $this->bus->send($command, (new SendOptions())->sendLocal());
                }
            } finally {
                $this->isHandling = false;
            }
        }
    }
}
