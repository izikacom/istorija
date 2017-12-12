<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface TracksChanges
{
    public function getId();
    public function hasChanges(): bool;
}
