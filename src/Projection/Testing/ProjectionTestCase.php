<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Projection\Testing;

use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use Dayuse\Istorija\Projection\Projection;
use Dayuse\Istorija\Utils\EnsureFailed;
use PHPUnit\Framework\TestCase;

abstract class ProjectionTestCase extends TestCase
{
    /**
     * @var Scenario
     */
    protected $scenario;

    protected function setUp()
    {
        parent::setUp();

        $dao        = new InMemoryDAO();
        $projection = $this->createProjection($dao);

        $this->scenario = new class($projection, $dao, $this) extends Scenario
        {
            /**
             * @var TestCase
             */
            private $testCase;

            public function __construct(Projection $projection, DAOInterface $dao, TestCase $testCase)
            {
                parent::__construct($projection, $dao);

                $this->testCase = $testCase;;
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

    abstract protected function createProjection(DAOInterface $dao): Projection;
}