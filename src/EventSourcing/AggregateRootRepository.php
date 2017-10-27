<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 04/10/2017
 * Time: 17:54
 */

namespace Dayuse\Istorija\EventSourcing;


use Dayuse\Istorija\Identifiers\Identifier;

interface AggregateRootRepository
{
    public function get(Identifier $aggregateId);
    public function save(AggregateRoot $aggregateRoot);
}