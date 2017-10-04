<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 04/10/2017
 * Time: 17:54
 */

namespace DayUse\Istorija\EventSourcing;


use DayUse\Istorija\Identifiers\Identifier;

interface AggregateRootRepository
{
    public function get(Identifier $aggregateId);
    public function save(AggregateRoot $aggregateRoot, $context = null);
}