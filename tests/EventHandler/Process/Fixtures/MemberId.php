<?php

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures;

use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;

class MemberId extends PrefixedUuidIdentifier
{
    protected static function prefix(): string
    {
        return 'member';
    }

    public static function generateFromApplicant(ApplicantId $applicantId)
    {
        return self::generateFrom((string)$applicantId);
    }
}
