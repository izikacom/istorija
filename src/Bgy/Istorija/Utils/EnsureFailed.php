<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\Utils;

use Assert\InvalidArgumentException;
use Bgy\Istorija\Exception;

class EnsureFailed extends InvalidArgumentException implements Exception {}
