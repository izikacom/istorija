<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:28
 */

namespace Dayuse\Istorija\CommandBus;

use Dayuse\Istorija\SimpleMessaging\Bus;
use Dayuse\Istorija\Utils\Ensure;

class CommandBusValidator
{
    /**
     * @var Bus
     */
    private $commandBus;

    /**
     * @var CommandValidator[]
     */
    private $validators;

    /**
     * CommandBusValidator constructor.
     *
     * @param Bus                $commandBus
     * @param CommandValidator[] $validators
     */
    public function __construct(Bus $commandBus, array $validators)
    {
        Ensure::allIsInstanceOf($validators, CommandValidator::class);

        $this->commandBus = $commandBus;
        $this->validators = $validators;
    }

    public function subscribe(string $commandType, callable $callable)
    {
        $this->commandBus->subscribe($commandType, $callable);
    }

    public function handle(Command $command)
    {
        /** @var CommandValidator $validator */
        foreach ($this->validators as $validator) {
            if (false === $validator->support($command)) {
                continue;
            }

            $validator->validate($command);
        }

        $this->commandBus->publish($command);
    }
}
