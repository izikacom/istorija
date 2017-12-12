<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

interface AggregateRoot extends RecordsEvents, TracksChanges, IsEventSourced, IsVersioned, Entity
{
}
