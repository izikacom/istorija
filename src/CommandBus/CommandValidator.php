<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 05/10/2017
 * Time: 11:26
 */

namespace Dayuse\Istorija\CommandBus;

use Dayuse\Istorija\CommandBus\Validator\Exception\CommandNotValidException;

interface CommandValidator
{
    /**
     * @param Command $command
     *
     * @throws CommandNotValidException
     */
    public function validate(Command $command);

    public function support(Command $command) : bool;
}
