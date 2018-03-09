<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures;

use Dayuse\Istorija\EventHandler\Process\StatefulProcess;
use Dayuse\Istorija\EventHandler\State;
use Dayuse\Test\Istorija\EventHandler\Process\Fixtures\Command\CreateMember;
use Dayuse\Test\Istorija\EventHandler\Process\Fixtures\Event\ApplicationAccepted;
use Dayuse\Test\Istorija\EventHandler\Process\Fixtures\Event\ApplicationRefused;
use Dayuse\Test\Istorija\EventHandler\Process\Fixtures\Event\ApplicationRegistered;

class CreateMemberWhenApplicantAcceptedStatefulProcess extends StatefulProcess
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
