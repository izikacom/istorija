<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Serializer;

use Dayuse\Istorija\Exception;

class CorruptedSerializedMessage extends \RuntimeException implements Exception
{
    public function __construct(?string $optionalInformation = null)
    {
        parent::__construct($optionalInformation);
    }
}
