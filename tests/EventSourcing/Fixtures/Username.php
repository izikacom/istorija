<?php

namespace DayUse\Test\Istorija\EventSourcing\Fixtures;

use Assert;

class Username
{
    private $username;

    /**
     * Creates a username from string
     *
     * @param $username
     */
    public function __construct($username)
    {
        Assert\that($username)->string()->betweenLength(1, 20);
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }
}
