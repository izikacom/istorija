<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process\Fixtures\Event;


use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Test\Istorija\Process\Fixtures\ApplicantId;

class ApplicationRefused implements DomainEvent
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