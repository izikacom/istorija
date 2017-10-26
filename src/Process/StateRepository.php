<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\Process;


interface StateRepository
{
    public function save(State $state) : void;
    public function find(ProcessId $processId) : State;
}