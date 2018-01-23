<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface RecordsEvents
{
    public function getRecordedEvents();
    public function clearRecordedEvents(): void;
    public function hasRecordedEvents(): bool;
}
