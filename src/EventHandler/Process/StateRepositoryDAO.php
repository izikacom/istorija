<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\EventHandler\Process;

use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\EventHandler\State;
use Dayuse\Istorija\Utils\Ensure;

class StateRepositoryDAO implements StateRepository
{
    private const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /** @var DAOInterface */
    private $dao;

    public function __construct(DAOInterface $dao)
    {
        $this->dao = $dao;
    }

    public function save(ProcessId $processId, State $state): void
    {
        $this->dao->save($processId, [
            'closedAt' => null,
            'state'    => $state->toArray(),
        ]);
    }

    public function close(ProcessId $processId): void
    {
        $previousState = $this->find($processId);
        $closingDate   = \DateTimeImmutable::createFromFormat(
            'U.u',
            sprintf('%.6F', microtime(true)),
            new \DateTimeZone('UTC')
        );

        $this->dao->save($processId, [
            'closedAt' => $closingDate->format(self::DATE_FORMAT),
            'state'    => $previousState->toArray(),
        ]);
    }

    public function find(ProcessId $processId): State
    {
        $data = $this->dao->find($processId);

        Ensure::notNull($data, sprintf('State process not found; %s', $processId));
        Ensure::null($data['closedAt'], sprintf('State have been already processed by %s', $processId));

        return State::fromArray($data['state']);
    }
}
