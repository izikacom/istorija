<?php

namespace Dayuse\Test\Istorija\Utils;

use Dayuse\Istorija\Utils\UtcDateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UtcDateTimeImmutableTest extends TestCase
{
    /**
     * @dataProvider provideElapsedDateTime
     */
    public function testDateIsElapsed(string $timestamp): void
    {
        $this->assertTrue((new UtcDateTimeImmutable($timestamp))->isElapsed());
    }

    public function provideElapsedDateTime(): iterable
    {
        yield ['2018-03-15 16:09:32'];
        yield ['yesterday'];
        yield ['-10 minutes'];
        yield ['-5 seconds'];
        yield ['-1 second'];
        yield ['now'];
    }

    /**
     * @dataProvider provideNotElapsedDateTime
     */
    public function testDateIsNotElapsed(string $timestamp): void
    {
        $this->assertFalse((new UtcDateTimeImmutable($timestamp))->isElapsed());
    }

    public function provideNotElapsedDateTime(): iterable
    {
        yield ['+1 second'];
        yield ['+30 seconds'];
        yield ['+1 minute'];
        yield ['+1 day'];
        yield ['tomorrow'];
        yield ['next monday'];
    }
}