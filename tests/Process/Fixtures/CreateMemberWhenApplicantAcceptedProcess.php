<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process\Fixtures;


use Dayuse\Istorija\CommandBus\CommandBus;
use Dayuse\Istorija\Process\AutoNameProcessTrait;
use Dayuse\Istorija\Process\Process;
use Dayuse\Istorija\Process\State;
use Dayuse\Istorija\Process\StateRepository;
use Dayuse\Test\Istorija\Process\Fixtures\Command\CreateMember;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationAccepted;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRefused;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRegistered;

class CreateMemberWhenApplicantAcceptedProcess extends Process
{
    use AutoNameProcessTrait;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var StateRepository
     */
    private $repository;

    public function __construct(CommandBus $commandBus, StateRepository $repository)
    {
        $this->commandBus = $commandBus;
        $this->repository = $repository;
    }

    public function whenApplicationRegistered(ApplicationRegistered $event)
    {
        $processId = $this->getProcessIdFromAggregate($event->getApplicationId());
        $state     = new State($processId, [
            'applicationId' => (string)$event->getApplicationId(),
        ]);

        $this->repository->save($state);
    }

    public function whenApplicationAccepted(ApplicationAccepted $event)
    {
        $processId = $this->getProcessIdFromAggregate($event->getApplicationId());
        $state     = $this->repository->find($processId);

        $this->commandBus->handle(new CreateMember(
            MemberId::generateFromApplicant(ApplicantId::fromString($state->get('applicationId')))
        ));

        $state->done();

        $this->repository->save($state);
    }

    public function whenApplicationRefused(ApplicationRefused $event)
    {
        $processId = $this->getProcessIdFromAggregate($event->getApplicationId());
        $state     = $this->repository->find($processId);

        $state->done();

        $this->repository->save($state);
    }
}