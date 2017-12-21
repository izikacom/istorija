<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:07
 */

namespace Dayuse\Test\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\Storage\InMemoryDAO;
use Dayuse\Test\Istorija\DAO\DAOTestCase;

class InMemoryDAOTest extends DAOTestCase
{
    protected function createDAO()
    {
        return new InMemoryDAO();
    }

    /**
     * @test
     */
    public function it_can_be_transferred_to_another_DAO()
    {
        $DAO = $this->createDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $targetDAO = new InMemoryDAO();

        $DAO->transferTo($targetDAO);

        $this->assertEquals($targetDAO->findAll(), $DAO->findAll());
    }
}
