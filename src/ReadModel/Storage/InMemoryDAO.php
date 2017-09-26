<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:59
 */

namespace DayUse\Istorija\ReadModel\Storage;

use DayUse\Istorija\ReadModel\AdvancedDAOInterface;
use DayUse\Istorija\ReadModel\BulkableInterface;
use DayUse\Istorija\ReadModel\DAOInterface;
use DayUse\Istorija\ReadModel\FunctionalTrait;
use DayUse\Istorija\ReadModel\IdentifiableValue;
use DayUse\Istorija\ReadModel\TransferableInterface;


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
    public function save(string $id, array $model)
    {
        $this->data[$id] = $model;
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
    public function findAll(int $page = 0, int $maxPerPage = 50)
    {
        return array_slice($this->data, $page, $maxPerPage, false);
    }

    /**
     * {@inheritDoc}
     */
    public function countAll()
    {
        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function transferTo(DAOInterface $otherDAO)
    {
        if (0 === count($this->data)) {
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
        foreach ($this->data as $id => $model) {
            $otherDAO->save($id, $model);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $id)
    {
        unset($this->data[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->data = [];
    }
}