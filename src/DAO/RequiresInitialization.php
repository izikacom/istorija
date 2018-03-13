<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\DAO;

interface RequiresInitialization
{
    public function initialize(): void;
}
