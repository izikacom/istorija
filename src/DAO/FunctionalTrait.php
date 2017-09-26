<?php
/**
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * Date: 08/07/2017
 * Time: 13:54
 */

namespace DayUse\Istorija\DAO;


trait FunctionalTrait
{
    /**
     * @param string   $id
     * @param callable $updateMethod
     * @param bool     $allowCreation
     */
    public function update(string $id, callable $updateMethod, bool $allowCreation = true)
    {
        /** @var DAOInterface $that */
        $that = $this;

        $data = $that->find($id);

        if(false === $allowCreation && null === $data) {
            throw new \InvalidArgumentException('Trying to update a not found value.');
        }

        if (null === $data) {
            $data = [];
        }

        $updatedData = $updateMethod($data);

        $that->save($id, $updatedData);
    }
}