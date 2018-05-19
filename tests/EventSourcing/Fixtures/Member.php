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

    private function __construct(MemberId $memberId)
    {
        $this->memberId = $memberId;
    }

    public static function register(MemberId $memberId, Username $username, Email $email): self
    {
        $member = new self($memberId);

        $member->recordThat(MemberRegistered::fromArray([
            'memberId' => (string) $memberId,
            'username' => (string) $username,
            'email'    => (string) $email,
            'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));

        return $member;
    }

    public function applyMemberRegistered(MemberRegistered $event): void
    {
        $this->memberId = MemberId::fromString($event->memberId);
        $this->username = new Username($event->username);
        $this->email = new Email($event->email);
//        $this->registrationState = new RegistrationState(RegistrationState::STATE_PENDING_CONFIRMATION);
        $this->registeredAt = new DateTimeImmutable($event->date);
    }

    public function confirmEmail(): void
    {
        $this->recordThat(MemberConfirmedEmail::fromArray([
            'memberId' => $this->memberId
        ]));
    }

    public function applyMemberConfirmedEmail(MemberConfirmedEmail $event): void
    {
        // Nothing to do
    }

    public function createTask(TaskId $taskId): void
    {
        if (array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task already created');
        }

        $this->captureEntity(Task::create($this->memberId, $taskId));
    }

    public function applyTaskCreated(TaskCreated $event): void
    {
        $task = $this->registerEntity(Task::class, $event);
        $this->tasks[(string)$event->taskId] = $task;
    }

    public function completeTask(TaskId $taskId): void
    {
        if (!array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task never created');
        }

        $this->tasks[(string)$taskId]->complete();
    }

    public function deleteTask(TaskId $taskId): void
    {
        if (!array_key_exists((string) $taskId, $this->tasks)) {
            throw new \DomainException('Task never created');
        }

        $this->tasks[(string)$taskId]->delete();
    }

    public function applyTaskDeleted(TaskDeleted $event): void
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
