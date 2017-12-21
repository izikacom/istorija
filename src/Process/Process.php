<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\EventSourcing\EventHandler;
use Dayuse\Istorija\Identifiers\Identifier;

interface Process extends EventHandler
{
    public function getProcessId(Identifier $identifier) : ProcessId;
    public function getName(): string;
}
