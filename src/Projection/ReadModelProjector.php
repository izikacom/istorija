<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 17:15
 */

namespace DayUse\Istorija\Projection;

use DayUse\Istorija\ReadModel\DAOInterface;

abstract class ReadModelProjector extends Projector
{
    abstract protected function getReadModel(): DAOInterface;
}