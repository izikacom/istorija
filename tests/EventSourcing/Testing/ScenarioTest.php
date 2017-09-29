<?php

namespace DayUse\Test\Istorija\EventSourcing\Testing;

use DateTimeImmutable;
use DayUse\Istorija\EventSourcing\Testing\Scenario;
use DayUse\Istorija\Utils\Ensure;
use DayUse\Test\Istorija\EventSourcing\Fixtures\Email;
use DayUse\Test\Istorija\EventSourcing\Fixtures\Member;
use DayUse\Test\Istorija\EventSourcing\Fixtures\MemberConfirmedEmail;
use DayUse\Test\Istorija\EventSourcing\Fixtures\MemberId;
use DayUse\Test\Istorija\EventSourcing\Fixtures\MemberRegistered;
use DayUse\Test\Istorija\EventSourcing\Fixtures\Username;
use PHPUnit\Framework\TestCase;

class ScenarioTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_work_with_aggregate_root_class()
    {
        $memberId = MemberId::generate();

        $scenario = Scenario::startFromClass(Member::class);
        $scenario->given([
            MemberRegistered::fromArray([
                'memberId' => $memberId,
                'username' => 'thomas',
                'email'    => 'thomas@tourlourat.com',
                'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]),
        ]);
        $scenario->when(function (Member $member) {
            $member->confirmEmail();
        });

        $scenario->then([
            MemberConfirmedEmail::class,
        ]);

        $scenario->then([
            MemberConfirmedEmail::fromArray([
                'memberId' => (string)$memberId,
            ]),
        ]);

        $scenario->then([
            function (MemberConfirmedEmail $event) use ($memberId) {
                Ensure::eq($memberId, $event->memberId);

                return true;
            },
        ]);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_clone_aggregate_instance()
    {
        $memberId = MemberId::generate();
        $member   = Member::register($memberId, new Username('thomas'), new Email('thomas@tourlourat.com'));

        $scenario = Scenario::startFromInstance($member);
        $scenario->when(function (Member $member) {
        });

        $member->confirmEmail();

        $scenario->then([
        ]);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_work_without_events()
    {
        $memberId = MemberId::generate();
        $member   = Member::register($memberId, new Username('thomas'), new Email('thomas@tourlourat.com'));

        $scenario = Scenario::startFromInstance($member);
        $scenario->when(function (Member $member) {
        });

        $scenario->then([
        ]);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_should_work_with_aggregate_root_instance()
    {
        $memberId = MemberId::generate();
        $member   = Member::register($memberId, new Username('thomas'), new Email('thomas@tourlourat.com'));

        $scenario = Scenario::startFromInstance($member);
        $scenario->when(function (Member $member) {
            $member->confirmEmail();
        });

        $scenario->then([
            MemberConfirmedEmail::class,
        ]);

        $this->assertTrue(true);
    }
}
