<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process;

use Dayuse\Istorija\EventSourcing\AbstractEventHandler;
use Dayuse\Istorija\Identifiers\Identifier;
use Dayuse\Istorija\Utils\Ensure;

abstract class AbstractProcess extends AbstractEventHandler implements Process
{
    public function getProcessId(Identifier $identifier) : ProcessId
    {
        return ProcessId::generateFromIdentifier($this->getName(), $identifier);
    }

    public function getName(): string
    {
        $class = trim(\get_class($this), "\\");

        if (strpos($class, "\\") === false) {
            return $class;
        }

        $parts                 = explode("\\", $class);
        $className             = end($parts);
        $classNamePieces       = preg_split('/(?=[A-Z])/', $className);
        $usefulClassNamePieces = array_slice($classNamePieces, 1, -1);

        Ensure::eq('Process', $classNamePieces[count($classNamePieces) - 1], 'When using AutoNameProcessTrait, your class must end with Process.');

        return strtolower(implode('-', $usefulClassNamePieces));
    }
}
