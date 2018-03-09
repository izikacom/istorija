<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\Utils\State;

interface StateRepository
{
    public function save(ProcessId $processId, State $state) : void;
    public function close(ProcessId $processId) : void;
    public function find(ProcessId $processId) : State;
}
