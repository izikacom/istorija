<?php

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use DateTimeImmutable;
use Dayuse\Istorija\EventSourcing\AbstractAggregateRoot;
use Dayuse\Istorija\Identifiers\Identifier;

class Member extends AbstractAggregateRoot
{
    /** @var MemberId */
    private $memberId;
    /** @var Username */
    private $username;
    /** @var Email */
    private $email;
    /** @var \DateTime */
    private $registeredAt;

    /** @var Task[] */
    private $tasks = [];

    public static function register(MemberId $memberId, Username $username, Email $email)
    {
        $member = new Member();
        $member->recordThat(MemberRegistered::fromArray([
            'memberId' => (string) $memberId,
            'username' => (string) $username,
            'email'    => (string) $email,
            'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));

        return $member;
    }

    public function applyMemberRegistered(MemberRegistered $event)
    {
        $this->memberId = MemberId::fromString($event->memberId);
        $this->username = new Username($event->username);
        $this->email = new Email($event->email);
//        $this->registrationState = new RegistrationState(RegistrationState::STATE_PENDING_CONFIRMATION);
        $this->registeredAt = new DateTimeImmutable($event->date);
    }

    public function confirmEmail()
    {
        $this->recordThat(MemberConfirmedEmail::fromArray([
            'memberId' => $this->memberId
        ]));
    }

    public function applyMemberConfirmedEmail(MemberConfirmedEmail $event)
    {
        // Nothing to do
    }

    public function createTask(TaskId $taskId)
    {
        if (array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task already created');
        }

        $this->captureEntity(Task::create($this->memberId, $taskId));
    }

    public function applyTaskCreated(TaskCreated $event)
    {
        $task = $this->registerEntity(Task::class, $event);
        $this->tasks[(string)$event->taskId] = $task;
    }

    public function completeTask(TaskId $taskId)
    {
        if (!array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task never created');
        }

        $this->tasks[(string)$taskId]->complete();
    }

    public function deleteTask(TaskId $taskId)
    {
        if (!array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task never created');
        }

        $this->tasks[(string)$taskId]->delete();
    }

    public function applyTaskDeleted(TaskDeleted $event)
    {
        unset($this->tasks[(string)$event->taskId]);

        $this->releaseEntity($event->taskId, $event);
    }

    /**
     * @inheritDoc
     */
    protected function getEntities()
    {
        return array_values($this->tasks);
    }

    /**
     * Used for testing purpose only.
     *
     * @internal
     */
    public function getTasks()
    {
        return array_values($this->tasks);
    }

    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @return Identifier
     */
    public function getId()
    {
        return $this->memberId;
    }
}
