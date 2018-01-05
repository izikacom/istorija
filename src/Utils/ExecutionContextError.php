<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

use Dayuse\Istorija\Exception;

class ExecutionContextError extends \LogicException implements Exception
{
    public static function missingKey(string $key): self
    {
        return new self(sprintf('The "%s" is missing from current ExecutionContext', $key));
    }
}
