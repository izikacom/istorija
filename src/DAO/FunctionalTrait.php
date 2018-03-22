<?php
namespace Dayuse\Istorija\DAO;

use Dayuse\Istorija\Utils\Ensure;

trait FunctionalTrait
{
    public function update(string $id, callable $updateMethod, bool $allowCreation = true) : void
    {
        /** @var DAOInterface $that */
        $that = $this;

        $data = $that->find($id);

        if (false === $allowCreation) {
            Ensure::notNull($data, 'Trying to update a not found value.');
        }

        if (null === $data) {
            $updatedData = $updateMethod();
        } else {
            $updatedData = $updateMethod($data);
        }

        $that->save($id, $updatedData);
    }
}
