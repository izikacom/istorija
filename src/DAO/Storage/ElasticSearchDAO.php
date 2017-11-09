<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:04
 */

namespace Dayuse\Istorija\DAO\Storage;


use Dayuse\Istorija\DAO\AdvancedDAOInterface;
use Dayuse\Istorija\DAO\BulkableInterface;
use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\IdentifiableValue;
use Dayuse\Istorija\DAO\SearchableInterface;
use Dayuse\Istorija\Utils\Ensure;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ElasticSearchDAO implements AdvancedDAOInterface, SearchableInterface, BulkableInterface
{
    use FunctionalTrait;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * ElasticSearchDAO constructor.
     *
     * @param Client $client
     * @param string $index
     * @param string $type
     */
    public function __construct(Client $client, $index, $type)
    {
        Ensure::string($type);

        $this->client = $client;
        $this->index  = $index;
        $this->type   = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $id, $data)
    {
        Ensure::isArray($data, 'ElasticSearch was tested only with value as array');

        $params = [
            'index'   => $this->index,
            'type'    => $this->type,
            'id'      => $id,
            'body'    => $data,
            'refresh' => true,
        ];

        $this->client->index($params);
    }

    /**
     * @inheritDoc
     */
    public function saveBulk(array $models)
    {
        Ensure::allIsInstanceOf($models, IdentifiableValue::class);
        Ensure::allSatisfy($models, function (IdentifiableValue $identifiableValue) {
            return is_array($identifiableValue->getValue());
        }, 'ElasticSearch was tested only with value as array');

        $params = [
            'refresh' => true,
        ];

        /** @var IdentifiableValue $model */
        foreach ($models as $i => $model) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    '_type'  => $this->type,
                    '_id'    => $model->getId(),
                ],
            ];
            $params['body'][] = $model->getValue();

            if (($i + 1) % 1000 == 0) {
                $this->client->bulk($params);

                // erase the old bulk request
                $params = ['body' => []];
            }
        }

        if (!empty($params['body'])) {
            $this->client->bulk($params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $id)
    {
        $params = [
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $id,
        ];

        try {
            $result = $this->client->get($params);
        } catch (Missing404Exception $e) {
            return null;
        }

        return $this->deserializeHit($result);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll($page = 0, $maxPerPage = 50)
    {
        return $this->query($this->buildSearchQuery(), $page, $maxPerPage);
    }

    /**
     * @see    https://qbox.io/blog/an-introduction-to-ngrams-in-elasticsearch
     *
     * {@inheritDoc}
     */
    public function search($text = null, array $criteria = [], $page = 0, $maxPerPage = 50)
    {
        $query = $this->buildSearchQuery($text, $criteria);

        return $this->query($query, $page, $maxPerPage);
    }

    /**
     * @inheritDoc
     */
    public function countAll()
    {
        return $this->doCount($this->buildSearchQuery());
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $id)
    {
        try {
            $this->client->delete([
                'id'      => $id,
                'index'   => $this->index,
                'type'    => $this->type,
                'refresh' => true,
            ]);
        } catch (Missing404Exception $e) { // It was already deleted or never existed, fine by us!
        }
    }

    /**
     * * If you want to use the findBy method; note that you have to define custom mapping and set the field as not analyzed.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/guide/current/_finding_exact_values.html
     *
     * {@inheritDoc}
     */
    protected function findBy(array $criteria = [], $page = 0, $maxPerPage = 50)
    {
        if (empty($criteria)) {
            return [];
        }

        return $this->query(
            $this->buildSearchQuery(null, $criteria),
            $page,
            $maxPerPage
        );
    }

    protected function findOneBy(array $criteria = [])
    {
        $results = $this->findBy($criteria);

        if (count($results) === 0) {
            return null;
        }

        return reset($results);
    }

    /**
     * Input could be a string or an array.
     *
     * 1. string : will execute a best effort full search
     * 2. array : will execute a search on each fields. keys are field & values..
     *
     * @param string|array $input
     * @param array        $criteria
     *
     * @return array
     *
     * Exemple :
     *
     * $criteria = [
     *   'date' => [
     *      '>' => '2017-01-05T14:37:10+0000',
     *      '<' => '2017-03-05T14:37:10+0000',
     *   ],
     *   'price' => [
     *      '>' => '3000',
     *   ]
     *   'model' => 'Commode Torben',
     *   'company' => [
     *      '=' => 'Ikea',
     *   ],
     * ]
     *
     *
     * POST view-model-tests/users/_search
     * {
     *   "query": {
     *         "bool": {
     *             "filter": {
     *                 "match": { "_all": "super"}
     *             },
     *             "must": [
     *                 {
     *                     "term": {
     *                         "createdAt.raw": "2016-09-19T14:43:45+0000"
     *                     }
     *                 },
     *                 {
     *                     "range": {
     *                         "createdAt.raw": {
     *                             "lte": "2016-09-19T14:43:45+0000"
     *                         }
     *                     }
     *                 },
     *                 {
     *                     "term": {
     *                         "roles.raw": "super_admin"
     *                     }
     *                 }
     *             ]
     *         }
     *   }
     * }
     *
     *
     *
     *
     */
    protected function buildSearchQuery($input = null, array $criteria = [])
    {
        $query = [
            'bool' => $this->buildMatching($input),
        ];

        if ($criteria) {
            $query['bool']['filter'] = $this->buildFilters($criteria);
        }

        return $query;
    }

    /**
     * The input could be either a string or an array.
     *
     * If array; keys will be used as field and values for matching.
     *
     * @param string|array $input
     *
     * @return array
     */
    protected function buildMatching($input)
    {
        if ($input) {
            if (is_string($input)) {
                return [
                    "must" => [
                        "match" => [
                            "_all" => [
                                "query"    => $input,
                                "operator" => "and",
                            ],
                        ],
                    ],
                ];
            } else {
                Ensure::allString(array_values($input));
                Ensure::allString(array_keys($input));

                $mustFactory = function ($field, $text) {
                    return [
                        'match' => [
                            $field => [
                                "query"    => $text,
                                "operator" => "and",
                            ],
                        ],
                    ];
                };

                return [
                    "minimum_should_match" => 1,
                    "should"               => [
                        array_map($mustFactory, array_keys($input), array_values($input)),
                    ],
                ];
            }

        } else {
            return [
                "must" => [
                    "match_all" => [],
                ],
            ];
        }
    }

    /**
     * When using a empty condition; there is not build filter.
     * ie: ['initiator' => null]
     * ie: ['initiator' => '']
     *
     * If you want to filter using null value; you should create well-formed conditions before
     * ie: ['initiator' => ['=' => null]] (not yet implemented)
     * ie: ['initiator' => ['!=' => null]] (not yet implemented)
     *
     * @param array $criterias
     *
     * @return array
     */
    protected function buildFilters(array $criterias = [])
    {
        return array_values(array_filter(array_map(function ($field, $conditions) {
            if (empty($conditions)) {
                return null;
            }

            if (is_scalar($conditions)) {
                $conditions = [
                    '=' => $conditions,
                ];
            }

            return $this->buildFilter($field, $conditions);
        }, array_keys($criterias), $criterias)));
    }

    protected function buildFilter($field, array $conditions)
    {
        $rangeFactory = function ($field, array $conditions) {
            $operators = [
                '>'  => 'gt',
                '>=' => 'gte',
                '<'  => 'lt',
                '<=' => 'lte',
            ];

            $parameters = [];
            foreach ($conditions as $operator => $value) {
                if (!array_key_exists($operator, $operators)) {
                    throw new \InvalidArgumentException('Invalid range operator : ' . $operator);
                }

                $parameters[$operators[$operator]] = $value;
            }

            return [
                'range' => [
                    $field => $parameters,
                ],
            ];
        };

        $operators = array_keys($conditions);
        if (in_array('=', $operators, true)) {
            return [
                'term' => [
                    $field => $conditions['='],
                ],
            ];
        }

        return $rangeFactory($field, $conditions);
    }

    /**
     * @inheritDoc
     */
    protected function countBy(array $criteria = [])
    {
        if (empty($criteria)) {
            return 0;
        }

        return $this->doCount($this->buildSearchQuery(null, $criteria));
    }

    /**
     * This method require the DeleteByQuery plugin
     *
     * If the operation is time consuming. Consider using bulk deletion or using the Delete-By-Query plugin.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/plugins/2.0/plugins-delete-by-query.html
     */
    public function flush()
    {
        $params = [
            "search_type" => "scan",    // use search_type=scan
            "scroll"      => "2s",     // how long between scroll requests. should be small!
            "size"        => 50,        // how many results *per shard* you want back
            "index"       => $this->index,
            "type"        => $this->type,
            "body"        => [
                "query" => ['match_all' => []],
            ],
        ];

        try {
            $docs = $this->client->search($params);   // Execute the search
        } catch (Missing404Exception $e) {
            return;
        }

        $scroll_id = $docs['_scroll_id'];   // The response will contain no results, just a _scroll_id

        // Now we loop until the scroll "cursors" are exhausted
        while (\true) {

            // Execute a Scroll request
            $response = $this->client->scroll([
                    "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
                    "scroll"    => "2s"           // and the same timeout window
                ]
            );

            // Check to see if we got any search hits from the scroll
            if (count($response['hits']['hits']) > 0) {
                // If yes, Do Work Here
                array_walk($response['hits']['hits'], function ($hit) {
                    $this->remove($hit['_id']);
                });

                // Get new scroll_id
                // Must always refresh your _scroll_id!  It can change sometimes
                $scroll_id = $response['_scroll_id'];
            } else {
                // No results, scroll cursor is empty.  You've exported all the data
                break;
            }
        }
    }

    private function searchAndDeserializeHits(array $query)
    {
        try {
            $result = $this->client->search($query);
        } catch (Missing404Exception $e) {
            return [];
        }

        if (!array_key_exists('hits', $result)) {
            return [];
        }

        return $this->deserializeHits($result['hits']['hits']);
    }

    private function doCount($query = [])
    {
        try {
            $result = $this->client->count(
                [
                    'index' => $this->index,
                    'type'  => $this->type,
                    'body'  => [
                        'query' => $query,
                    ],
                ]
            );
        } catch (Missing404Exception $e) {
            return 0;
        }

        if (!array_key_exists('count', $result)) {
            return 0;
        }

        return $result['count'];
    }

    protected function query(array $query, $page, $maxPerPage = 50)
    {
        return $this->searchAndDeserializeHits(
            [
                'index' => $this->index,
                'type'  => $this->type,
                'body'  => [
                    'query' => $query,
                    'sort'  => $this->defaultSorting(),
                ],
                'size'  => $maxPerPage,
                'from'  => $maxPerPage * $page,
            ]
        );
    }

    private function deserializeHit(array $hit)
    {
        return $hit['_source'];
    }

    private function deserializeHits(array $hits)
    {
        return array_map([$this, 'deserializeHit'], $hits);
    }

    /**
     * Define explicit mapping for type.
     * Due to ElasticSearch limitation. So be aware that mapping could not be updated.
     *
     * Thanks to ElasticSearch implicit mapping; you are not forced to call this method.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     *
     * @return boolean True, if the index was successfully created
     */
    public function defineMapping()
    {
        $indexParams = [
            'index' => $this->index,
            'type'  => $this->type,
        ];

        $indexParams['body'] = $this->mappingRules();

        $this->client->indices()->putMapping($indexParams);

        $response = $this->client->cluster()->health([
            'index'           => $this->index,
            'wait_for_status' => 'yellow',
            'timeout'         => '5s',
        ]);

        return isset($response['status']) && $response['status'] !== 'red';
    }

    /**
     * Override this method if you want to define custom fine mapping rules.
     *
     * Note that you should not_index fileds that are used for filtering.
     * 'email'     => [
     *     'type'   => 'string',
     *     "fields" => [
     *         "raw" => [
     *             'type'  => 'string',
     *             'index' => 'not_analyzed',
     *         ],
     *
     *     ],
     * ],
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     *
     * @return array
     */
    protected function mappingRules()
    {
        return [
            '_source' => [
                'enabled' => true,
            ],
        ];
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html
     *
     * [
     *    'score' => [
     *       'order' => 'desc',
     *    ],
     * ]
     *
     * @return array
     */
    protected function defaultSorting()
    {
        return [];
    }

    final protected function getConnection(): Client
    {
        return $this->client;
    }
}