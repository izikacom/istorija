<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:24
 */

namespace Dayuse\Istorija\DAO;

interface SearchableInterface extends AdvancedDAOInterface
{
    public function search(Pagination $pagination, array $criteria = [], string $text = null) : array;
    public function filter(Pagination $pagination, array $criteria = []) : array;
    public function countResults(array $criteria = [], string $text = null) : int;
}
