<?php

namespace Bgy\Istorija\EventStore\Storage;

/**
 * @author Boris Guéry <guery.b@gmail.com>
 */
interface SupportsTransaction
{
    public function beginTransaction();
    public function commit();
}
