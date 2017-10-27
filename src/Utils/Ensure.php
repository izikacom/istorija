<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

use Assert\Assertion;

class Ensure extends Assertion
{
    protected static $exceptionClass = EnsureFailed::class;
}
