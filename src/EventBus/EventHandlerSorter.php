<?php

namespace Dayuse\Istorija\EventBus;


interface EventHandlerSorter
{
    public function sort(iterable $eventHandlers): array;
}