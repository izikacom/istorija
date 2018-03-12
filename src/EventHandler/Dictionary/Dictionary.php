<?php

namespace Dayuse\Istorija\EventHandler\Dictionary;

use Dayuse\Istorija\DAO\DAOInterface;


/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class Dictionary
{
    /**
     * @var DAOInterface
     */
    private $dao;

    public function __construct(DAOInterface $dao)
    {
        $this->dao = $dao;
    }
    
    public function save(string $id, array $data): void
    {
        $this->dao->save($id, $data);
    }

    public function get(string $id)
    {
        return $this->dao->find($id);
    }

    public function remove(string $id): void
    {
        $this->dao->remove($id);
    }

    public function flush(): void
    {
        $this->dao->flush();
    }

}