<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore\Storage;

interface RequiresInitialization
{
    public function initialize(): void;
}
