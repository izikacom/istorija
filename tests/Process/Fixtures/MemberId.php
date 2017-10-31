<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */

namespace Dayuse\Test\Istorija\Process\Fixtures;


use Dayuse\Istorija\Identifiers\PrefixedUuidIdentifier;

class MemberId extends PrefixedUuidIdentifier
{
    static protected function prefix()
    {
        return 'member';
    }

    static public function generateFromApplicant(ApplicantId $applicantId)
    {
        return self::generateFrom((string)$applicantId);
    }
}