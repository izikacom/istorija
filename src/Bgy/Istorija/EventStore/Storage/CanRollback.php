<?php

namespace Bgy\Istorija\EventStore\Storage;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
interface CanRollback
{
    public function rollback();
}
