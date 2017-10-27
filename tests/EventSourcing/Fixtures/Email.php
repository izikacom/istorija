<?php

namespace Dayuse\Test\Istorija\EventSourcing\Fixtures;

use Assert\Assertion;

class Email
{
    private $email;

    /**
     * Creates an email from string
     *
     * @param string $email
     */
    public function __construct($email)
    {
        Assertion::email($email);
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }
}
