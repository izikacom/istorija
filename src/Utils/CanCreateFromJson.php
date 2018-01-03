<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Utils;

interface CanCreateFromJson
{
    static public function fromJson(string $jsonString);
}
