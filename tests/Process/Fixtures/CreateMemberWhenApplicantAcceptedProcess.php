<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process\Fixtures;


use Dayuse\Istorija\Process\SimpleProcess;
use Dayuse\Istorija\Process\State;
use Dayuse\Test\Istorija\Process\Fixtures\Command\CreateMember;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationAccepted;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRefused;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRegistered;

class CreateMemberWhenApplicantAcceptedProcess extends SimpleProcess
{
    public function whenApplicationRegistered(ApplicationRegistered $event)
    {
        $this->initState($event->getApplicationId(), new State([
            'applicationId' => (string)$event->getApplicationId(),
        ]));
    }

    public function whenApplicationAccepted(ApplicationAccepted $event)
    {
        $state = $this->getState($event->getApplicationId());

        $this->handleCommand(new CreateMember(
            MemberId::generateFromApplicant(ApplicantId::fromString($state->get('applicationId')))
        ));

        $this->closeState($event->getApplicationId());
    }

    public function whenApplicationRefused(ApplicationRefused $event)
    {
        $this->closeState($event->getApplicationId());
    }
}