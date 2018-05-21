<?php

namespace Dayuse\Test\Istorija\Identifiers;

use Dayuse\Istorija\Identifiers\UuidIdentifier;
use PHPUnit\Framework\TestCase;

class UuidIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_must_be_predictive(): void
    {
        $this->assertSame('6300d7e2-5267-5b94-b54c-07b92aae72c0', (string) PredictiveId::generateFrom('booking-b3dc7470-08f6-44ba-99ef-a28d288a9912'));
    }
}

class PredictiveId extends UuidIdentifier
{
    protected static function prefix(): string
    {
        return 'predictive';
    }
}
