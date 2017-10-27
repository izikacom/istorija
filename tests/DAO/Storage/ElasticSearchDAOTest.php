<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:33
 */

namespace Dayuse\Test\Istorija\DAO\Storage;


use Dayuse\Istorija\DAO\Storage\ElasticSearchDAO;
use Dayuse\Test\Istorija\DAO\DAOTestCase;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;

class ElasticSearchDAOTest extends DAOTestCase
{
    /**
     * @var Client
     */
    private $client;

    private function checkElasticSearch()
    {
        try {
            $this->client->ping();
        }
        catch(NoNodesAvailableException $e) {
            $this->markTestSkipped('ElasticSearch does not pong.');
        }

    }

    protected function createDAO()
    {
        $this->client = $this->createClient();

        $this->checkElasticSearch();

        $this->client->indices()->create(['index' => 'test_index']);
        $this->client->cluster()->health(['index' => 'test_index', 'wait_for_status' => 'yellow', 'timeout' => '10s']);

        return $this->createElasticSearchDAO(
            $this->client,
            'test_index',
            'Broadway\DAO\DAOTestReadModel'
        );
    }

    protected function createElasticSearchDAO(Client $client, $index, $class)
    {
        return new ElasticSearchDAO($client, $index, $class);
    }

    /**
     * @test
     */
    public function it_creates_an_index_with_non_analyzed_terms()
    {
        $type             = 'class';
        $nonAnalyzedTerm  = 'name';
        $index            = 'test_non_analyzed_index';
        $this->DAO = new ElasticSearchDAO(
            $this->client,
            $index,
            $type,
            [$nonAnalyzedTerm]
        );

//        $this->DAO->createIndex();
        $this->client->cluster()->health(['index' => $index, 'wait_for_status' => 'yellow', 'timeout' => '10s']);
        $mapping = $this->client->indices()->getMapping(['index' => $index]);

        $this->assertArrayHasKey($index, $mapping);
        $this->assertArrayHasKey($type, $mapping[$index]['mappings']);
        $nonAnalyzedTerms = [];

        foreach ($mapping[$index]['mappings'][$type]['properties'] as $key => $value) {
            $nonAnalyzedTerms[] = $key;
        }

        $this->assertEquals([$nonAnalyzedTerm], $nonAnalyzedTerms);
    }

    public function tearDown()
    {
        $this->client->indices()->delete(['index' => 'test_index']);

        if ($this->client->indices()->exists(['index' => 'test_non_analyzed_index'])) {
            $this->client->indices()->delete(['index' => 'test_non_analyzed_index']);
        }
    }

    /**
     * @return Client
     */
    private function createClient()
    {
        return ClientBuilder::fromConfig(['hosts' => ['localhost:9200']]);
    }
}