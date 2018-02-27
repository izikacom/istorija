<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:27
 */

namespace Dayuse\Istorija\CommandBus\Validator\Exception;

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\Exception;
use Verraes\ClassFunctions\ClassFunctions;

class CommandNotValidException extends \RuntimeException implements Exception
{
    /** @var Command */
    private $command;

    /** @var string */
    private $reason;

    public function __construct(Command $command, string $reason = null, \Throwable $previous = null)
    {
        $this->command = $command;
        $this->reason  = $reason;

        if($this->reason) {
            $message = sprintf('%s could not be handled because of %s',
                ClassFunctions::fqcn($command),
                $this->reason
            );
        }
        else {
            $message = sprintf('%s could not be handled.',
                ClassFunctions::fqcn($command)
            );
        }

        parent::__construct($message, 0, $previous);
    }

    public function getCommand(): Command
    {
        return $this->command;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
