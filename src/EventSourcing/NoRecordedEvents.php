<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventSourcing;

use Dayuse\Istorija\Exception;

class NoRecordedEvents extends \DomainException implements Exception {}
