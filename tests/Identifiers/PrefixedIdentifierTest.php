<?php

namespace Dayuse\Test\Istorija\Identifiers;

use Dayuse\Istorija\Identifiers\PrefixedIdentifier;
use PHPUnit\Framework\TestCase;

class PrefixedIdentifierTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_return_canonical_matching_pattern(): void
    {
        $this->assertSame('customer-(?P<identifier>.+)', CustomerId::getCanonicalMatchingPattern());
    }

    /**
     * @test
     */
    public function it_should_match_pattern(): void
    {
        $this->assertTrue(CustomerId::isMatchingPattern('customer-id'));
        $this->assertTrue(CustomerId::isMatchingPattern('customer-lol'));
        $this->assertTrue(CustomerId::isMatchingPattern('customer-123'));

        $this->assertFalse(CustomerId::isMatchingPattern('customer-'));
        $this->assertFalse(CustomerId::isMatchingPattern('customer'));
    }
}

class CustomerId extends PrefixedIdentifier
{
    public static function generate()
    {
        return new static('customer-id');
    }

    protected static function prefix(): string
    {
        return 'customer';
    }
}