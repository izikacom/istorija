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

        return $this;
    }

    public function when(array $handlers): Query
    {
        $this->query->when($handlers);

        return $this;
    }
}