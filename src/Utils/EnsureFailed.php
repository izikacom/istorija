<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\Utils;

use Assert\InvalidArgumentException;
use DayUse\Istorija\Exception;

class EnsureFailed extends InvalidArgumentException implements Exception {}
