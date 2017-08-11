<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventSourcing;

interface IsEventSourced
{
    public static function reconstituteFrom();
}
