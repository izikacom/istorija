<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 29/03/2017
 * Time: 11:03
 */

namespace DayUse\Istorija\DAO\Proxy;

use DayUse\Istorija\DAO\DAOInterface;
use DayUse\Istorija\DAO\FunctionalTrait;
use DayUse\Istorija\DAO\TransferableInterface;

/**
 * Class Buffer
 *
 * @package DayUse\Istorija\DAO\Proxy
 */
class Buffer implements DAOInterface
{
    use FunctionalTrait;

    /**
     * @var DAOInterface
     */
    private $targetedDAO;

    /**
     * @var DAOInterface|TransferableInterface
     */
    private $bufferDAO;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * InMemoryBuffer constructor.
     *
     * @param DAOInterface                       $targetedDAO
     * @param DAOInterface|TransferableInterface $bufferDAO
     */
    public function __construct(DAOInterface $targetedDAO, DAOInterface $bufferDAO)
    {
        if (!$bufferDAO instanceof TransferableInterface) {
            throw new \InvalidArgumentException('The buffered DAO have to be transferable.');
        }

        $this->targetedDAO = $targetedDAO;
        $this->bufferDAO   = $bufferDAO;
        $this->enabled     = false;
    }

    static public function create(DAOInterface $targetedDAO, DAOInterface $bufferDAO)
    {
        return new Buffer($targetedDAO, $bufferDAO);
    }

    public function enable()
    {
        $this->bufferDAO->flush();
        $this->enabled = true;
    }

    public function flushAndCommit()
    {
        $this->targetedDAO->flush();
        $this->bufferDAO->transferTo($this->targetedDAO);
    }

    public function commit()
    {
        $this->bufferDAO->transferTo($this->targetedDAO);
    }

    /**
     * @inheritDoc
     */
    public function save(string $id, $data)
    {
        if ($this->enabled) {
            return $this->bufferDAO->save($id, $data);
        }

        // stream to real DAO
        return $this->targetedDAO->save($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function find(string $id)
    {
        if ($this->enabled) {
            return $this->bufferDAO->find($id);
        }

        // stream to real DAO
        return $this->targetedDAO->find($id);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $id)
    {
        if ($this->enabled) {
            $this->bufferDAO->remove($id);

            return;
        }

        // stream to real DAO
        $this->targetedDAO->remove($id);
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        if ($this->enabled) {
            $this->bufferDAO->flush();

            return;
        }

        // stream to real DAO
        $this->targetedDAO->flush();
    }

    /**
     * @return DAOInterface
     */
    public function getTargetedDAO(): DAOInterface
    {
        return $this->targetedDAO;
    }

    /**
     * @return DAOInterface|TransferableInterface
     */
    public function getBufferDAO()
    {
        return $this->bufferDAO;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
