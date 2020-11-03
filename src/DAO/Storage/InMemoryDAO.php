<?php
namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\AdvancedDAOInterface;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\DAOInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\DAO\Pagination;
use Dayuse\Istorija\DAO\TransferableInterface;

/**
 * In-memory implementation of a read model DAO.
 *
 * The in-memory DAO is useful for testing code.
 */
class InMemoryDAO implements AdvancedDAOInterface, TransferableInterface
{
    use FunctionalTrait;

    private $data = [];

    /**
     * {@inheritDoc}
     */
    public function save(string $id, $data) : void
    {
        $this->data[$id] = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $id)
    {
        return $this->data[$id] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(Pagination $pagination): array
    {
        return \array_slice(
            $this->data,
            $pagination->getOffset(),
            $pagination->getMaxPerPage(),
            false
        );
    }

    /**
     * {@inheritDoc}
     */
    public function countAll(): int
    {
        return \count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function transferTo(DAOInterface $otherDAO) : void
    {
        if (0 === \count($this->data)) {
            // nothing to save
            return;
        }

        if ($otherDAO instanceof BulkableInterface) {
            // can bulk
            $otherDAO->saveBulk(array_map(function (string $id, array $value) {
                return new IdentifiableValue($id, $value);
            }, array_keys($this->data), array_values($this->data)));

            return;
        }

        // save one by one
        foreach ($this->data as $id => $data) {
            $otherDAO->save($id, $data);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $id) : void
    {
        unset($this->data[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function flush() : void
    {
        $this->data = [];
    }
}
