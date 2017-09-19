<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore\Storage;

interface RequiresInitialization
{
    public function initialize(): void;
}
