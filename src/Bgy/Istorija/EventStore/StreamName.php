<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\Identifiers\Identifier;
use Bgy\Istorija\Utils\Contract;
use Bgy\Istorija\Utils\Ensure;

class StreamName
{
    private $identifier;
    private $contract;

    public function __construct(Identifier $identifier, Contract $contract)
    {
        $this->identifier = $identifier;
        $this->contract = $contract;
    }

    public static function fromString(string $canonicalStreamName)
    {
        $splitStreamName = explode('-', $canonicalStreamName);
        Ensure::count(
            $splitStreamName,
            2,
            'Canonical Stream must be a string containing the UUID and the Contract separated by a dash (-)'
        );

        return new self(
            Identifier::fromString($splitStreamName[0]),
            Contract::with($splitStreamName[1])
        );
    }

    public function getIdentifier(): Identifier
    {
        return $this->identifier;
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function getCanonicalStreamName(): string
    {
        return sprintf('%s-%s', $this->identifier, $this->contract);
    }

    public function __toString(): string
    {
        return $this->getCanonicalStreamName();
    }
}
