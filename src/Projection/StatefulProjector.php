<?php

namespace Dayuse\Istorija\Projection;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\Identifiers\Identifier;

/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
abstract class StatefulProjector extends Projector
{
    /** @var DAOInterface */
    private $dao;

    public function __construct(DAOInterface $dao)
    {
        $this->dao = $dao;
    }

    protected function getState(Identifier $identifier): State
    {
        $data = $this->dao->find($identifier);

        return State::fromArray($data ?? []);
    }

    protected function setState(Identifier $identifier, callable $updateMethod): State
    {
        $currentState = $this->getState($identifier);

        /** @var State $nextState */
        $nextState    = $updateMethod($currentState);

        $this->dao->save($identifier, $nextState->toArray());
    }


}