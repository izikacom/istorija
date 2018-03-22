<?php
namespace Dayuse\Istorija\DAO;

/**
 * Class IdentifiableValue
 *
 * This class is used mainly for bulking process.
 *
 * @package Dayuse\Istorija\DAO
 */
class IdentifiableValue
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $value;

    /**
     * IdentifiableValue constructor.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __construct(string $id, $value)
    {
        $this->id    = $id;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
