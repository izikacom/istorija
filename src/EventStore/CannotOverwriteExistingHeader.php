<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore;

use Dayuse\Istorija\Exception;

class CannotOverwriteExistingHeader extends \DomainException implements Exception
{
    public function __construct(Header $header)
    {
        parent::__construct(sprintf('Header: %s is already defined and cannot be overwritten', $header->getName()));
    }
}
