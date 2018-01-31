<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:28
 */

namespace Dayuse\Istorija\CommandBus;

use Dayuse\Istorija\Utils\Ensure;

class CommandBusValidator implements CommandBus
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var CommandValidator[]
     */
    private $validators;

    /**
     * CommandBusValidator constructor.
     *
     * @param CommandBus         $commandBus
     * @param CommandValidator[] $validators
     */
    public function __construct(CommandBus $commandBus, array $validators)
    {
        Ensure::allIsInstanceOf($validators, CommandValidator::class);

        $this->commandBus = $commandBus;
        $this->validators = $validators;
    }

    public function register(string $commandType, callable $callable): void
    {
        $this->commandBus->register($commandType, $callable);
    }

    /**
     * @inheritdoc
     */
    public function handle(Command $command): void
    {
        /** @var CommandValidator $validator */
        foreach ($this->validators as $validator) {
            if (false === $validator->support($command)) {
                continue;
            }

            $validator->validate($command);
        }

        $this->commandBus->handle($command);
    }
}
