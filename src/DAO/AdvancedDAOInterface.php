<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:24
 */

namespace Dayuse\Istorija\DAO;

interface AdvancedDAOInterface extends DAOInterface
{
    public function findAll(int $page = 0, int $maxPerPage = 50): iterable;
    public function countAll(): int;
}
