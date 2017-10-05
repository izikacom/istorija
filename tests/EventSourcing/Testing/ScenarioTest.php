<?php

namespace DayUse\Test\Istorija\EventSourcing\Testing;

use DateTimeImmutable;
use DayUse\Istorija\EventSourcing\AggregateRoot;
use DayUse\Istorija\EventSourcing\Testing\Scenario;
use DayUse\Istorija\Identifiers\GenericUuidIdentifier;
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
        $scenario = Scenario::monitor(Member::class);

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
    public function it_should_work_with_aggregate_root_instance()
    {
        $memberId = MemberId::generate();
        $member   = Member::register($memberId, new Username('thomas'), new Email('thomas@tourlourat.com'));

        $scenario = Scenario::monitorAndStartFromInstance($member);
        $scenario->when(function (Member $member) {
            $member->confirmEmail();
        });

        $scenario->then([
            MemberConfirmedEmail::class,
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

        $scenario = Scenario::monitorAndStartFromInstance($member);
        $scenario->when(function (Member $member) {
        });

        $scenario->then([
        ]);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function test_factory_method()
    {
        $scenario = Scenario::monitor(Member::class);
        $scenario->when(function () {
            $member = Member::register(MemberId::generate(), new Username('thomas'), new Email('thomas@tourlourat.com'));
            $member->confirmEmail();

            return $member;
        });

        $scenario->then([
            MemberRegistered::class,
            MemberConfirmedEmail::class,
        ]);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function without_given_pass_when_must_return_aggregate_root()
    {
        $this->expectException(\InvalidArgumentException::class);

        $scenario = Scenario::monitor(Member::class);
        $scenario->when(function () {
            // not returned value
        });

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function without_given_pass_when_must_return_aggregate_root_correspondig_to_the_monitored_class()
    {
        $this->expectException(\InvalidArgumentException::class);

        $scenario = Scenario::monitor(Member::class);
        $scenario->when(function () {
            return new class() extends AggregateRoot {
                public function getId() {
                    return GenericUuidIdentifier::generate();
                }
            };
        });

        $this->assertTrue(true);
    }
}
