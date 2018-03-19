<?php
namespace Dayuse\Istorija\DAO\Proxy;

use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use Dayuse\Istorija\DAO\TransferableInterface;
use Dayuse\Istorija\Utils\Ensure;

/**
 * Class Buffer
 *
 * @package Dayuse\Istorija\DAO\Proxy
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
    public function __construct(DAOInterface $targetedDAO, DAOInterface $bufferDAO = null)
    {
        Ensure::nullOrIsInstanceOf($bufferDAO, TransferableInterface::class, 'The buffered DAO have to be transferable.');

        $this->targetedDAO = $targetedDAO;
        $this->bufferDAO   = $bufferDAO ?? new InMemoryDAO();
        $this->enabled     = false;
    }

    public static function create(DAOInterface $targetedDAO, DAOInterface $bufferDAO)
    {
        return new Buffer($targetedDAO, $bufferDAO);
    }

    public function enable(): void
    {
        $this->bufferDAO->flush();
        $this->enabled = true;
    }

    public function flushAndCommit(): void
    {
        $this->targetedDAO->flush();
        $this->bufferDAO->transferTo($this->targetedDAO);
    }

    public function commit(): void
    {
        $this->bufferDAO->transferTo($this->targetedDAO);
    }

    /**
     * @inheritDoc
     */
    public function save(string $id, $data): void
    {
        if ($this->enabled) {
            $this->bufferDAO->save($id, $data);

            return;
        }

        // stream to real DAO
        $this->targetedDAO->save($id, $data);
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
    public function remove(string $id): void
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
    public function flush(): void
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
