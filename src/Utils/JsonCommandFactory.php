<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

use Dayuse\Istorija\CommandBus\Command;

class JsonCommandFactory
{
    static public function create(string $contract, string $commandPayload)
    {
        Ensure::isJsonString($commandPayload);

        $commandContract = Contract::with($contract);

        if (!is_subclass_of($commandContract->toClassName(), Command::class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Command "%s" doesn\'t implements %s', $commandContract, Command::class
                )
            );
        }

        if (!is_subclass_of($commandContract->toClassName(), CanCreateFromJson::class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Command "%s" cannot be created from JSON', $commandContract
                )
            );
        }

        json_decode($commandPayload);
        if (json_last_error()) {

            throw new \InvalidArgumentException("Invalid JSON payload");
        }

        return call_user_func(sprintf("%s::fromJson", $commandContract->toClassName()), $commandPayload);
    }
}
