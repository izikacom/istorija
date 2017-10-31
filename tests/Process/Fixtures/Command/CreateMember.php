<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process\Fixtures\Command;


use Dayuse\Istorija\CommandBus\Command;
use Dayuse\Test\Istorija\Process\Fixtures\MemberId;

class CreateMember implements Command
{
    /**
     * @var MemberId
     */
    private $memberId;

    public function __construct(MemberId $memberId)
    {
        $this->memberId = $memberId;
    }

    public function getMemberId(): MemberId
    {
        return $this->memberId;
    }
}