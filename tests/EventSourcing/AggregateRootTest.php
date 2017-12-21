<?php

namespace Dayuse\Test\Istorija\EventSourcing;

use Dayuse\Test\Istorija\EventSourcing\Fixtures\Email;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\Member;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\MemberConfirmedEmail;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\MemberId;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\MemberRegistered;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\TaskCreated;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\TaskId;
use Dayuse\Test\Istorija\EventSourcing\Fixtures\Username;
use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_record_changes()
    {
        $memberId = MemberId::generate();
        $member = Member::register($memberId, new Username('foobar'), new Email('foo@bar.com'));
        $member->confirmEmail();

        $taskId = TaskId::generate();
        $member->createTask($taskId);

        $recordedEvents = $member->getRecordedEvents();
        $this->assertTrue($member->hasRecordedEvents());
        $this->assertCount(3, $recordedEvents);
        $this->assertInstanceOf(MemberRegistered::class, $recordedEvents[0]);
        $this->assertInstanceOf(MemberConfirmedEmail::class, $recordedEvents[1]);
        $this->assertInstanceOf(TaskCreated::class, $recordedEvents[2]);
    }

    /**
     * @test
     */
    public function it_should_clear_recorded_events_when_asked()
    {
        $memberId = MemberId::generate();
        $member = Member::register($memberId, new Username('foobar'), new Email('foo@bar.com'));
        $member->confirmEmail();

        $taskId = TaskId::generate();
        $member->createTask($taskId);

        $member->clearRecordedEvents();

        $recordedEvents = $member->getRecordedEvents();
        $this->assertCount(0, $recordedEvents);
        $this->assertFalse($member->hasRecordedEvents());
    }

    /**
     * @test
     */
    public function it_should_reconstitute_from_history()
    {
        $memberId = MemberId::generate();
        $member = Member::register($memberId, new Username('foobar'), new Email('foo@bar.com'));
        $member->confirmEmail();

        $this->assertCount(0, $member->getTasks());

        $taskId = TaskId::generate();
        $member->createTask($taskId);

        // assert that aggregate record task.
        $this->assertFalse($member->getTasks()[0]->isCompleted());

        $member->completeTask($taskId);

        // assert that aggregate record task.
        $this->assertTrue($member->getTasks()[0]->isCompleted());

        $recordedEvents = $member->getRecordedEvents();
        $this->assertCount(4, $recordedEvents);
        $member->clearRecordedEvents();
        $reconstitutedMember = Member::reconstituteFromHistory($recordedEvents);

        $this->assertEquals($member, $reconstitutedMember);
        $this->assertCount(1, $member->getTasks());
        $this->assertCount(1, $reconstitutedMember->getTasks());

        $this->assertTrue($member->getTasks()[0]->isCompleted());
        $this->assertTrue($reconstitutedMember->getTasks()[0]->isCompleted());

        $reconstitutedMember->deleteTask($taskId);
        // assert that aggregate record task deletion.
        $this->assertCount(0, $reconstitutedMember->getTasks());
        $this->assertCount(1, $reconstitutedMember->getRecordedEvents());
    }
}
