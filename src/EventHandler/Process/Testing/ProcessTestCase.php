<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\EventHandler\Process\Testing;

use Dayuse\Istorija\CommandBus\CommandBus;
use Dayuse\Istorija\CommandBus\NullCommandBus;
use Dayuse\Istorija\CommandBus\TraceableCommandBus;
use Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use Dayuse\Istorija\EventHandler\Process\Process;
use Dayuse\Istorija\EventHandler\Process\StateRepository;
use Dayuse\Istorija\EventHandler\Process\StateRepositoryDAO;
use Dayuse\Istorija\Utils\EnsureFailed;
use PHPUnit\Framework\TestCase;

abstract class ProcessTestCase extends TestCase
{
    /** @var Scenario */
    protected $scenario;

    /** @var Process */
    protected $process;

    protected function setUp()
    {
        parent::setUp();

        $commandBus = new TraceableCommandBus(new NullCommandBus());
        $process    = $this->createProcess(
            $commandBus,
            new StateRepositoryDAO(new InMemoryDAO())
        );

        $this->scenario = new class($process, $commandBus, $this) extends Scenario {
            /**
             * @var TestCase
             */
            private $testCase;

            public function __construct(Process $process, TraceableCommandBus $commandBus, TestCase $testCase)
            {
                parent::__construct($process, $commandBus);

                $this->testCase = $testCase;
                ;
            }

            public function then(array $allThen = [])
            {
                try {
                    parent::then($allThen);
                    $this->testCase->assertTrue(true);
                } catch (EnsureFailed $e) {
                    $this->testCase->fail($e->getMessage());
                }
            }
        };
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    abstract protected function createProcess(CommandBus $commandBus, StateRepository $repository): Process;
}
