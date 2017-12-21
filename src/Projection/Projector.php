<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 25/09/2017
 * Time: 16:18
 */

namespace Dayuse\Istorija\Projection;

use Dayuse\Istorija\EventSourcing\AbstractEventHandler;
use Dayuse\Istorija\EventSourcing\DomainEvent\DomainEvent;
use Dayuse\Istorija\EventSourcing\DomainEvent\EventNameGuesser;

abstract class Projector extends AbstractEventHandler implements Projection
{
}
