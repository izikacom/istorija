<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\Utils\Ensure;

class StateRepositoryDAO implements StateRepository
{
    /**
     * @var DAOInterface
     */
    private $dao;

    public function __construct(DAOInterface $dao)
    {
        $this->dao = $dao;
    }

    public function save(ProcessId $processId, State $state): void
    {
        $this->dao->save($processId, $state->toArray());
    }

    public function find(ProcessId $processId): State
    {
        $data = $this->dao->find($processId);

        Ensure::notNull($data, sprintf('State process not found; %s', $processId));

        $state = State::fromArray($data);

        Ensure::false($state->isClosed(), sprintf('State have been already processed by %s', $processId));

        return $state;
    }
}
