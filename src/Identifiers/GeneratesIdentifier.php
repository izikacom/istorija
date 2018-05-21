<?php

namespace Dayuse\Istorija\Identifiers;

interface GeneratesIdentifier
{
    /**
     * @return static
     */
    public static function generate();
}
