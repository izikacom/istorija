<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 20/09/2016
 * Time: 10:57
 */

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use DateTimeImmutable;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\Entity;

class Task extends Entity
{
    /**
     * @var MemberId
     */
    private $memberId;

    /**
     * @var TaskId
     */
    private $taskId;

    /**
     * @var boolean
     */
    private $completed;

    static public function guardCreation(MemberId $memberId, TaskId $taskId)
    {
        $errorDetected = false;
        if ($errorDetected) {
            throw new \DomainException('Task could not be created');
        }
    }

    static public function create(MemberId $memberId, TaskId $taskId)
    {
        $that = new self();

        $that->recordThat(TaskCreated::fromArray([
            'memberId'  => $memberId,
            'taskId'    => $taskId,
            'completed' => false,
            'date'      => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));

        return $that;
    }

    public function applyTaskCreated(TaskCreated $event)
    {
        $this->memberId  = $event->memberId;
        $this->taskId    = $event->taskId;
        $this->completed = $event->completed;
    }

    public function delete()
    {
        $this->recordThat(TaskDeleted::fromArray([
            'memberId' => $this->memberId,
            'taskId'   => $this->taskId,
            'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));
    }

    public function complete()
    {
        $this->recordThat(TaskCompleted::fromArray([
            'memberId' => $this->memberId,
            'taskId'   => $this->taskId,
            'date'     => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]));
    }

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    public function applyTaskCompleted(TaskCompleted $event)
    {
        $this->completed = true;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->taskId;
    }

    public function isEventCanBeApplied(DomainEvent $event)
    {
        if (false === property_exists($event, 'taskId')) {
            return false;
        }

        return $this->taskId->equals($event->{"taskId"});
    }
}