<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore\Storage;

interface RequiresInitialization
{
    public function initialize(): void;
}
