<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\EventHandler\Process\Fixtures;

use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;

class MemberId extends PrefixedUuidIdentifier
{
    protected static function prefix()
    {
        return 'member';
    }

    public static function generateFromApplicant(ApplicantId $applicantId)
    {
        return self::generateFrom((string)$applicantId);
    }
}
