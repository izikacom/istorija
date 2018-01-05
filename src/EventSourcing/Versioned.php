<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventSourcing;

interface Versioned
{
    public function getVersion(): int;
}
