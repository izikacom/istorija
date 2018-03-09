<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Istorija\Projection\Testing;

use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\Projection\Projection;
use Dayuse\Istorija\Utils\Ensure;

class Scenario
{
    /**
     * @var Projection
     */
    private $projection;

    /**
     * @var DAOInterface
     */
    private $dao;

    public function __construct(Projection $projection, DAOInterface $dao)
    {
        $this->projection = $projection;
        $this->dao        = $dao;
    }

    public function given(array $events)
    {
        Ensure::allIsInstanceOf($events, DomainEvent::class);

        foreach ($events as $event) {
            $this->projection->apply($event);
        }

        return $this;
    }

    public function when(DomainEvent $when)
    {
        $this->projection->apply($when);

        return $this;
    }

    public function then(array $allThen = [])
    {
        // $allThen contains expected data to check against dao
        // - key : identifier within DAO
        // - value : expected data

        foreach ($allThen as $identifier => $data) {
            Ensure::eq($data, $this->dao->find($identifier));
        }

        return $this;
    }
}
