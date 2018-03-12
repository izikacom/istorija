<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures;

use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;

class ApplicantId extends PrefixedUuidIdentifier
{
    protected static function prefix()
    {
        return 'applicant';
    }
}
