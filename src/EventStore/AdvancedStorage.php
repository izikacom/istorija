<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\EventStore;

interface AdvancedStorage
{
    public function supportsAdvancedReadQuery(AdvancedReadQuery $query);
    public function readUsingAdvancedQuery(AdvancedReadQuery $query);
}
