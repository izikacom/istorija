<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

interface StateRepository
{
    public function save(ProcessId $processId, State $state) : void;
    public function find(ProcessId $processId) : State;
}
