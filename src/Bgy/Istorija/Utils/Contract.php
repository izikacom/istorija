<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\Utils;

use Bgy\Istorija\Utils\Ensure;
use Verraes\ClassFunctions\ClassFunctions;

final class Contract
{
    private $contractName;

    private function __construct(string $contract)
    {
        $this->contractName = $contract;
    }

    public static function with($name)
    {
        Ensure::string($name);
        Ensure::betweenLength($name, 1, 255);

        return new Contract($name);
    }

    public static function canonicalFrom($object)
    {
        return Contract::with(
            ClassFunctions::canonical($object)
        );
    }

    public function toClassName()
    {
        return str_replace('.', '\\', $this->contractName);
    }

    public function __toString()
    {
        return $this->contractName;
    }

    public function equals(Contract $other)
    {
        return $this->contractName === $other->contractName;
    }
}
