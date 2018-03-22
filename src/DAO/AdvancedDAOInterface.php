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
    public function findAll(Pagination $pagination): array;
    public function countAll(): int;
}