<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface RecordsEvents
{
    public function getUncommitedEvents();
    public function clearUncommitedEvents(): void;
}
