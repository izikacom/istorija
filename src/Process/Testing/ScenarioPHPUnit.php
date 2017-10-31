<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Process\Testing;


use Dayuse\Istorija\CommandBus\TraceableCommandBus;
use Dayuse\Istorija\Process\Process;
use Dayuse\Istorija\Utils\EnsureFailed;
use PHPUnit\Framework\TestCase;

class ScenarioPHPUnit extends Scenario
{
    /**
     * @var TestCase
     */
    private $testCase;

    public function __construct(Process $process, TraceableCommandBus $commandBus, TestCase $testCase)
    {
        parent::__construct($process, $commandBus);

        $this->testCase = $testCase;;
    }

    public function then(array $allThen = [])
    {
        try {
            parent::then($allThen);
            $this->testCase->assertTrue(true);
        }
        catch(EnsureFailed $e) {
            $this->testCase->fail($e->getMessage());
        }
    }

}