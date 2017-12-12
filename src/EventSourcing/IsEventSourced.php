<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface IsEventSourced
{
    public static function reconstituteFrom($history);
}
