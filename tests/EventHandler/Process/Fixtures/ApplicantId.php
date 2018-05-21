<?php

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures;

use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;

class ApplicantId extends PrefixedUuidIdentifier
{
    protected static function prefix(): string
    {
        return 'applicant';
    }
}
