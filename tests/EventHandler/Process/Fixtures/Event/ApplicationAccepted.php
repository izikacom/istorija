<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures\Event;

use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Test\Istorija\EventHandler\Process\Fixtures\ApplicantId;

class ApplicationAccepted implements DomainEvent
{
    /**
     * @var ApplicantId
     */
    private $applicationId;

    public function __construct(ApplicantId $applicationId)
    {
        $this->applicationId = $applicationId;
    }

    public function getApplicationId(): ApplicantId
    {
        return $this->applicationId;
    }
}
