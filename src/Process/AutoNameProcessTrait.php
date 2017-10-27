<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace DayUse\Istorija\Process;


use DayUse\Istorija\Utils\Ensure;

trait AutoNameProcessTrait
{
    public function getName(): string
    {
        $class = trim(get_class($this), "\\");

        if (strpos($class, "\\") === false) {
            return $class;
        }

        $parts                 = explode("\\", $class);
        $className             = end($parts);
        $classNamePieces       = preg_split('/(?=[A-Z])/', $className);
        $usefulClassNamePieces = array_slice($classNamePieces, 1, -1);

        Ensure::eq('Process', $classNamePieces[count($classNamePieces) - 1], 'When using AutoNameProcessTrait, your class must end with Process.');

        return strtolower(join('-', $usefulClassNamePieces));
    }
}