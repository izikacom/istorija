<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

use Dayuse\Istorija\Utils\JsonPayloadDescription;

interface ProvideJsonPayloadDescription
{
    static public function getPayloadDescription(): JsonPayloadDescription;
}
