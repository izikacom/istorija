<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Utils;

use Assert\Assertion;

class Ensure extends Assertion
{
    protected static $exceptionClass = EnsureFailed::class;
}
