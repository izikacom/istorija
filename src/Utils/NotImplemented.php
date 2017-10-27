<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

use Dayuse\Istorija\Exception;

class NotImplemented extends \RuntimeException implements Exception
{
    public static function method(string $method): self
    {
        return new self(sprintf('Method "%s" not implemented', $method));
    }

    public static function feature(string $feature): self
    {
        return new self(sprintf('Feature "%s" not implemented', $feature));
    }
}
