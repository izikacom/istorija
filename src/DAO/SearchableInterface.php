<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:24
 */

namespace Dayuse\Istorija\DAO;

interface SearchableInterface extends DAOInterface
{
    public function search(string $text = null, array $criteria = [], int $page = 0, int $maxPerPage = 50) : iterable;
}
