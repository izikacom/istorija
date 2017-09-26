<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 17:15
 */

namespace DayUse\Istorija\Projection;

use DayUse\Istorija\DAO\DAOInterface;

abstract class PersistedProjector extends Projector
{
    abstract protected function getDAO(): DAOInterface;

    abstract protected function getName(): string;

    protected function updateState($state)
    {
        return $this->getDAO()->update($this->getName(), $state);
    }

    public function getState()
    {
        return $this->getDAO()->find($this->getName());
    }
}