<?php

namespace Dayuse\Istorija\DAO\Storage;

use Dayuse\Istorija\DAO\FunctionalTrait;
use Dayuse\Istorija\DAO\Pagination;
use Dayuse\Istorija\DAO\RequiresInitialization;
use Dayuse\Istorija\DAO\SearchableInterface;
use Dayuse\Istorija\Utils\Ensure;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ElasticSearchDAO implements SearchableInterface, RequiresInitialization
{
    use FunctionalTrait;

    /** @var Client */
    private $client;

    /** @var string */
    private $index;

    /** @var string */
    private $type;

    /** @var array */
    private $mapping;

    /** @var array */
    private $settings;

    /** @var array */
    private $sorting;

    /** @var string[] */
    private $multiMatchFields;

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping.html
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-sort.html
     */
    public function __construct(
        Client $client,
        string $index,
        string $type,
        array $mapping = null,
        array $settings = null,
        array $sorting = null,
        array $multiMatchFields = null
    ) {
        $this->client = $client;
        $this->index = $index;
        $this->type = $type;
        $this->mapping = $mapping;
        $this->settings = $settings;
        $this->sorting = $sorting;
        $this->multiMatchFields = $multiMatchFields;
    }

    /**
     * {@inheritDoc}
     */
    public function save(string $id, $data): void
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

            return $this->deserializeHit($result);
        } catch (Missing404Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(Pagination $pagination): array
    {
        return $this->query(
            $this->buildSearchQuery(),
            $pagination
        );
    }

    /**
     * @see    https://qbox.io/blog/an-introduction-to-ngrams-in-elasticsearch
     *
     * {@inheritDoc}
     */
    public function search(Pagination $pagination, array $criteria = [], string $text = null): array
    {
        $query = $this->buildSearchQuery(
            $text,
            $criteria
        );

        return $this->query($query, $pagination);
    }

    public function filter(Pagination $pagination, array $criteria = []): array
    {
        $query = $this->buildSearchQuery(
            null,
            $criteria
        );

        return $this->query($query, $pagination);
    }

    /**
     * @inheritDoc
     */
    public function countAll(): int
    {
        return $this->doCount($this->buildSearchQuery());
    }

    public function countResults(array $criteria = [], string $text = null): int
    {
        return $this->doCount($this->buildSearchQuery(
            $text,
            $criteria
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $id): void
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
     *                 "multi_match": { "fields": ["*"], "query": "Ikea", "operator": "and"}
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
    protected function buildSearchQuery($input = null, array $criteria = []): array
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
    protected function buildMatching($input): array
    {
        if (!$input) {
            return [
                'must' => [
                    'match_all' => (object)[],
                    // @see https://github.com/elastic/elasticsearch-php/issues/495#issuecomment-258533457
                ],
            ];
        }

        if (\is_string($input)) {
            return [
                'must' => [
                    'multi_match' => [
                        'fields'   => $this->multiMatchFields ?? ['*'], // search on all analyzed fields
                        'operator' => 'and',
                        'query'    => $input,
                    ],
                ],
            ];
        }

        if (\is_array($input)) {
            Ensure::allString(array_values($input));
            Ensure::allString(array_keys($input));

            $mustFactory = function ($field, $text) {
                return [
                    'match' => [
                        $field => [
                            'query'    => $text,
                            'operator' => 'and',
                        ],
                    ],
                ];
            };

            return [
                'minimum_should_match' => 1,
                'should'               => [
                    array_map($mustFactory, array_keys($input), array_values($input)),
                ],
            ];
        }

        throw new \InvalidArgumentException(sprintf('Not supported input type, given: %s', \gettype($input)));
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
    protected function buildFilters(array $criterias = []): array
    {
        return array_values(array_filter(array_map(function ($field, $conditions) {
            Ensure::notNull($conditions, sprintf('Could not build filters with a null value. Field: %s', $field));

            if (is_scalar($conditions)) {
                $conditions = [
                    '=' => $conditions,
                ];
            }

            return $this->buildFilter($field, $conditions);
        }, array_keys($criterias), $criterias)));
    }

    protected function buildFilter($field, array $conditions): array
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
        if (\in_array('=', $operators, true)) {
            return [
                'term' => [
                    $field => $conditions['='],
                ],
            ];
        }

        return $rangeFactory($field, $conditions);
    }

    /**
     * This method require the DeleteByQuery plugin
     *
     * If the operation is time consuming. Consider using bulk deletion or using the Delete-By-Query plugin.
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/plugins/2.0/plugins-delete-by-query.html
     */
    public function flush(): void
    {
        $params = [
            'scroll' => '2s',          // how long between scroll requests. should be small!
            'size'   => 50,               // how many results *per shard* you want back
            'index'  => $this->index,
            'body'   => [
                'query' => ['match_all' => new \stdClass()],
            ],
        ];

        // Execute the search
        // The response will contain the first batch of documents
        // and a scroll_id
        $response = $this->client->search($params);

        // Now we loop until the scroll "cursors" are exhausted
        while (isset($response['hits']['hits']) && \count($response['hits']['hits']) > 0) {
            array_walk($response['hits']['hits'], function ($hit) {
                $this->remove($hit['_id']);
            });

            // Get new scroll_id
            // You must always refresh your _scroll_id!  It can change sometimes
            $scroll_id = $response['_scroll_id'];

            // Execute a Scroll request and repeat
            $response = $this->client->scroll([
                    'scroll_id' => $scroll_id,  //...using our previously obtained _scroll_id
                    'scroll'    => '30s'           // and the same timeout window
                ]
            );
        }
    }

    private function doCount(array $query = []): int
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

    protected function query(array $query, Pagination $pagination): array
    {
        try {
            return $this->searchAndDeserializeHits(
                [
                    'index' => $this->index,
                    'type'  => $this->type,
                    'body'  => [
                        'query' => $query,
                        'sort'  => $this->sorting ?? [],
                    ],
                    'size'  => $pagination->getMaxPerPage(),
                    'from'  => $pagination->getMaxPerPage() * $pagination->getPage(),
                ]
            );
        } catch (Missing404Exception $e) {
            return [];
        }
    }

    private function searchAndDeserializeHits(array $query): array
    {
        $result = $this->client->search($query);

        if (!array_key_exists('hits', $result)) {
            return [];
        }

        return $this->deserializeHits($result['hits']['hits']);
    }

    private function deserializeHits(array $hits): array
    {
        return array_map([$this, 'deserializeHit'], $hits);
    }

    private function deserializeHit(array $hit): array
    {
        return $hit['_source'];
    }

    public function initialize(): void
    {
        $this->createIndex();
        $this->defineMapping();
    }

    public function deleteIndex(): void
    {
        try {
            $this->client->indices()->delete([
                'index' => $this->index,
            ]);
        } catch (Missing404Exception $e) {
        }
    }

    public function createIndex(): void
    {
        $this->client->indices()->create([
            'index' => $this->index,
            'body'  => [
                'settings' => $this->settings ?? $this->defaultSettings(),
            ],
        ]);
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
    public function defineMapping(): void
    {
        $indexParams = [
            'index' => $this->index,
            'type'  => $this->type,
        ];

        $indexParams['body'] = $this->mapping ?? $this->defaultMapping();

        $this->client->indices()->putMapping($indexParams);
    }

    protected function defaultMapping(): array
    {
        return [];
    }

    /**
     * @see    http://docs.searchkit.co/stable/docs/server/indexing.html
     * @see    https://qbox.io/blog/an-introduction-to-ngrams-in-elasticsearch
     * @see    https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-analyzers.html
     */
    protected function defaultSettings(): array
    {
        return [];
    }

    final public function getIndex(): string
    {
        return $this->index;
    }

    final public function getType(): string
    {
        return $this->type;
    }

    final public function getMapping(): array
    {
        return $this->mapping;
    }

    final public function getSettings(): array
    {
        return $this->settings;
    }

    final public function getConnection(): Client
    {
        return $this->client;
    }
}
