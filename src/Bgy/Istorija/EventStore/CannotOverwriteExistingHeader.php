<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\Istorija\EventStore;

use Bgy\Istorija\Exception;

class CannotOverwriteExistingHeader extends \DomainException implements Exception
{
    public function __construct(Header $header)
    {
        parent::__construct(sprintf('Header: %s is already defined and cannot be overwritten', $header->getName()));
    }
}
