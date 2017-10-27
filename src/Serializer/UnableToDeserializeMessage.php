<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Serializer;

use Dayuse\Istorija\Exception;

class UnableToDeserializeMessage extends \RuntimeException implements Exception
{
    public static function invalidMessageContract(string $messageContract): self
    {
        return new self(sprintf('The message contract "%s" could not be resolved', $messageContract));
    }
}
