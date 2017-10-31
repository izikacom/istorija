<?php

namespace Dayuse\Test\Istorija\Process\Testing;

use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Istorija\CommandBus\CommandBus;
use Dayuse\Istorija\Process\Process;
use Dayuse\Istorija\Process\StateRepository;
use Dayuse\Istorija\Process\Testing\TestCase;
use Dayuse\Istorija\Utils\Ensure;
use Dayuse\Test\Istorija\Process\Fixtures\ApplicantId;
use Dayuse\Test\Istorija\Process\Fixtures\Command\CreateMember;
use Dayuse\Test\Istorija\Process\Fixtures\CreateMemberWhenApplicantAcceptedProcess;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationAccepted;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRefused;
use Dayuse\Test\Istorija\Process\Fixtures\Event\ApplicationRegistered;
use Dayuse\Test\Istorija\Process\Fixtures\MemberId;

class ScenarioTest extends TestCase
{
    protected function createProcess(CommandBus $commandBus, StateRepository $repository): Process
    {
        return new CreateMemberWhenApplicantAcceptedProcess($commandBus, $repository);
    }

    /**
     * @test
     */
    public function it_should_not_handle_command_for_registration_phase()
    {
        $this->scenario->when(new ApplicationRegistered(ApplicantId::generate()));
        $this->scenario->then();
    }

    /**
     * @test
     */
    public function it_should_create_member_when_application_accepted_test_instance()
    {
        $applicantId = ApplicantId::generate();

        $this->scenario->given([
            new ApplicationRegistered($applicantId),
        ]);
        $this->scenario->when(new ApplicationAccepted($applicantId));
        $this->scenario->then([
            new CreateMember(MemberId::generateFromApplicant($applicantId)),
        ]);
    }

    /**
     * @test
     */
    public function it_should_create_member_when_application_accepted_test_class()
    {
        $applicantId = ApplicantId::generate();

        $this->scenario->given([
            new ApplicationRegistered($applicantId),
        ]);
        $this->scenario->when(new ApplicationAccepted($applicantId));
        $this->scenario->then([
            CreateMember::class,
        ]);
    }

    /**
     * @test
     */
    public function it_should_create_member_when_application_accepted_test_callable()
    {
        $applicantId = ApplicantId::generate();

        $this->scenario->given([
            new ApplicationRegistered($applicantId),
        ]);
        $this->scenario->when(new ApplicationAccepted($applicantId));
        $this->scenario->then([
            function (Command $command) use ($applicantId) {
                Ensure::isInstanceOf($command, CreateMember::class);
                Ensure::eq($command->getMemberId(), MemberId::generateFromApplicant($applicantId));
            },
        ]);
    }

    /**
     * @test
     */
    public function it_should_do_nothing_when_application_refused()
    {
        $applicantId = ApplicantId::generate();

        $this->scenario->given([
            new ApplicationRegistered($applicantId),
        ]);
        $this->scenario->when(new ApplicationRefused($applicantId));
        $this->scenario->then([]);
    }
}
