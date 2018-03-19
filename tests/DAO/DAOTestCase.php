<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:08
 */

namespace Dayuse\Test\Istorija\dao;

use Dayuse\Istorija\dao\daoInterface;
use Dayuse\Istorija\dao\Pagination;
use Dayuse\Istorija\dao\SearchableInterface;
use PHPUnit\Framework\TestCase;

abstract class DAOTestCase extends TestCase
{
    /** @var DAOInterface */
    protected $dao;

    protected function setUp()
    {
        $this->dao = $this->createDAO();
    }

    abstract protected function createDAO(): DAOInterface;

    /**
     * @test
     */
    public function it_saves_and_finds_read_models_by_id(): void
    {
        $model = $this->createReadModel('1', 'othillo', 'bar');

        $this->dao->save('1', $model);

        $this->assertEquals($model, $this->dao->find(1));
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found_on_empty_repo(): void
    {
        $this->assertEquals(null, $this->dao->find(2));
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found(): void
    {
        $model = $this->createReadModel('1', 'othillo', 'bar');

        $this->dao->save('1', $model);

        $this->assertNull($this->dao->find(2));
    }

    /**
     * @test
     */
    public function it_finds_by_name(): void
    {
        $this->checkSearchabledao();

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        $dao->save('1', $model1);
        $dao->save('2', $model2);

        $this->assertEquals([$model1], $dao->search(Pagination::firstPage(), ['name' => 'othillo']));
        $this->assertEquals([$model2], $dao->search(Pagination::firstPage(), ['name' => 'asm89']));
    }

    /**
     * @test
     */
    public function it_finds_by_one_element_in_array(): void
    {
        $this->checkSearchabledao();

        $model1 = $this->createReadModel('1', 'othillo', 'bar', ['elem1', 'elem2']);
        $model2 = $this->createReadModel('2', 'asm89', 'baz', ['elem3', 'elem4']);

        /** @var SearchableInterface $dao */
        $dao = $this->dao;


        $dao->save('1', $model1);
        $dao->save('2', $model2);

        $this->assertEquals([$model1], $dao->search(Pagination::firstPage(), ['array' => 'elem1']));
        $this->assertEquals([$model2], $dao->search(Pagination::firstPage(), ['array' => 'elem4']));
    }

    /**
     * @test
     */
    public function it_finds_if_all_clauses_match(): void
    {
        $this->checkSearchabledao();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $dao->save('1', $model1);
        $dao->save('2', $model2);

        $this->assertEquals([$model1], $dao->search(Pagination::firstPage(), ['name' => 'othillo', 'foo' => 'bar']));
        $this->assertEquals([$model2], $dao->search(Pagination::firstPage(), ['name' => 'asm89', 'foo' => 'baz']));
    }

    /**
     * @test
     */
    public function it_does_not_find_when_one_of_the_clauses_doesnt_match(): void
    {
        $this->checkSearchabledao();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $dao->save('1', $model1);
        $dao->save('2', $model2);

        $this->assertEquals([], $dao->search(Pagination::firstPage(), ['name' => 'othillo', 'foo' => 'baz']));
        $this->assertEquals([], $dao->search(Pagination::firstPage(), ['name' => 'asm89', 'foo' => 'bar']));
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_found_nothing(): void
    {
        $this->checkSearchabledao();

        $model1 = $this->createReadModel('1', 'othillo', 'bar');
        $model2 = $this->createReadModel('2', 'asm89', 'baz');

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $dao->save('1', $model1);
        $dao->save('2', $model2);

        $this->assertEquals([], $dao->search(Pagination::firstPage(), ['name' => 'Jan']));
    }

    /**
     * @test
     */
    public function it_returns_empty_array_when_searching_for_empty_array(): void
    {
        $this->checkSearchabledao();

        $model = $this->createReadModel('1', 'othillo', 'bar');

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $dao->save('1', $model);

        $this->assertEquals([], $dao->search(Pagination::firstPage()));
    }

    /**
     * @test
     */
    public function it_removes_a_readmodel(): void
    {
        $model = $this->createReadModel('1', 'John', 'Foo', ['foo' => 'bar']);

        /** @var SearchableInterface $dao */
        $dao = $this->dao;

        $dao->save('1', $model);
        $dao->remove('1');

        $this->assertEquals([], $dao->findAll(Pagination::firstPage()));
    }

    protected function createReadModel($id, $name, $foo, array $array = []): array
    {
        return [
            'id'    => $id,
            'name'  => $name,
            'foo'   => $foo,
            'array' => $array,
        ];
    }

    protected function checkSearchabledao(): void
    {
        if (!$this->dao instanceof SearchableInterface) {
            $this->markTestSkipped('Tested dao is not searchable');
        }
    }
}
