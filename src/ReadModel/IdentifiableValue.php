<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 20/04/2017
 * Time: 14:25
 */

namespace DayUse\Istorija\ReadModel;

/**
 * Class IdentifiableValue
 *
 * This class is used mainly for bulking process.
 *
 * @package DayUse\Istorija\ReadModel
 */
class IdentifiableValue
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $value;

    /**
     * IdentifiableValue constructor.
     *
     * @param string $id
     * @param array  $value
     */
    public function __construct(string $id, array $value)
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
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }
}