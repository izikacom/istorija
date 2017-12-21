<?php

namespace Dayuse\Istorija\Projection;

/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
class QueryBuilder
{
    /** @var Query */
    private $query;

    public function init(callable $callback): QueryBuilder
    {
        $this->query = new Query();
        $this->query->init($callback);

        return $this;
    }

    public function when(array $handlers): QueryBuilder
    {
        $this->query->when($handlers);

        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
