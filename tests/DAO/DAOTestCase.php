<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:08
 */

namespace DayUse\Test\Istorija\DAO;

use DayUse\Istorija\DAO\DAOInterface;
use DayUse\Istorija\DAO\SearchableInterface;
use PHPUnit\Framework\TestCase;

abstract class DAOTestCase extends TestCase
{
    /**
     * @var DAOInterface
     */
    protected $DAO;

    protected function setUp()
    {
        $this->DAO = $this->createDAO();
    }

    abstract protected function createDAO();

    /**
     * @test
     */
    public function it_saves_and_finds_read_models_by_id()
    {
        $model = $this->createReadModel('1', 'othillo', 'bar');

        $this->DAO->save('1', $model);

        $this->assertEquals($model, $this->DAO->find(1));
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found_on_empty_repo()
    {
        $this->assertEquals(null, $this->DAO->find(2));
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found()
    {
        $model = $this->createReadModel('1', 'othillo', 'bar');

        $this->DAO->save('1', $model);

        $this->assertNull($this->DAO->find(2));
    }

    /**
     * @test
     */
    public function it_finds_by_name()
    {
        $this->checkSearchableDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $this->assertEquals([$model1], $this->DAO->search(null, ['name' => 'othillo']));
        $this->assertEquals([$model2], $this->DAO->search(null, ['name' => 'asm89']));
    }

    /**
     * @test
     */
    public function it_finds_by_one_element_in_array()
    {
        $this->checkSearchableDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar', ['elem1', 'elem2']);
        $model2 = $this->createReadModel('2', 'asm89', 'baz', ['elem3', 'elem4']);

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $this->assertEquals([$model1], $this->DAO->search(null, ['array' => 'elem1']));
        $this->assertEquals([$model2], $this->DAO->search(null, ['array' => 'elem4']));
    }

    /**
     * @test
     */
    public function it_finds_if_all_clauses_match()
    {
        $this->checkSearchableDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $this->assertEquals([$model1], $this->DAO->search(null, ['name' => 'othillo', 'foo' => 'bar']));
        $this->assertEquals([$model2], $this->DAO->search(null, ['name' => 'asm89', 'foo' => 'baz']));
    }

    /**
     * @test
     */
    public function it_does_not_find_when_one_of_the_clauses_doesnt_match()
    {
        $this->checkSearchableDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $this->assertEquals([], $this->DAO->search(null, ['name' => 'othillo', 'foo' => 'baz']));
        $this->assertEquals([], $this->DAO->search(null, ['name' => 'asm89', 'foo' => 'bar']));
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_found_nothing()
    {
        $this->checkSearchableDAO();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $this->DAO->save('1', $model1);
        $this->DAO->save('2', $model2);

        $this->assertEquals([], $this->DAO->search(null, ['name' => 'Jan']));
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_searching_for_empty_array()
    {
        $this->checkSearchableDAO();

        $model = $this->createReadModel('1', 'othillo', 'bar');

        $this->DAO->save('1', $model);

        $this->assertEquals([], $this->DAO->search(null, []));
    }

    /**
     * @test
     */
    public function it_removes_a_readmodel()
    {
        $model = $this->createReadModel('1', 'John', 'Foo', ['foo' => 'bar']);
        $this->DAO->save('1', $model);

        $this->DAO->remove('1');

        $this->assertEquals([], $this->DAO->findAll());
    }

    protected function createReadModel($id, $name, $foo, array $array = [])
    {
        return [
            'id'    => $id,
            'name'  => $name,
            'foo'   => $foo,
            'array' => $array,
        ];
    }

    protected function checkSearchableDAO(): void
    {
        if (!$this->DAO instanceof SearchableInterface) {
            $this->markTestSkipped('Tested DAO is not searchable');
        }
    }
}