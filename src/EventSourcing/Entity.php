<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface Entity extends Identified
{
    public function getId();
}
