<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\EventHandler\Process;

use Dayuse\Istorija\EventHandler\State;

interface StateRepository
{
    public function save(ProcessId $processId, State $state) : void;
    public function close(ProcessId $processId) : void;
    public function find(ProcessId $processId) : State;
}
