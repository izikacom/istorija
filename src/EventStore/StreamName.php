<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace DayUse\Istorija\EventStore;

use DayUse\Istorija\Identifiers\Identifier;
use DayUse\Istorija\Utils\Contract;
use DayUse\Istorija\Utils\Ensure;

class StreamName
{
    const DELIMITER = '-';

    private $identifier;
    private $contract;

    public function __construct(Identifier $identifier, Contract $contract)
    {
        Ensure::eq(0, substr_count($contract, self::DELIMITER));

        $this->identifier = $identifier;
        $this->contract   = $contract;
    }

    public static function fromString(string $canonicalStreamName)
    {
        $splitStreamName = explode(self::DELIMITER, $canonicalStreamName);

        $contractName   = $splitStreamName[0];
        $identifierName = join(self::DELIMITER, array_slice($splitStreamName, 1));

        Ensure::notEmpty($contractName, sprintf('Canonical Stream must be a string containing the Contract and the identifier separated by a dash (%s)', self::DELIMITER));
        Ensure::notEmpty($identifierName, sprintf('Canonical Stream must be a string containing the Contract and the identifier separated by a dash (%s)', self::DELIMITER));

        return new self(
            Identifier::fromString($splitStreamName[1]),
            Contract::with($splitStreamName[0])
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
        return join(self::DELIMITER, [
            $this->contract,
            $this->identifier,
        ]);
    }

    public function __toString(): string
    {
        return $this->getCanonicalStreamName();
    }
}
