<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:24
 */

namespace Dayuse\Istorija\DAO;


interface SearchableInterface
{
    /**
     * search without parameters should be equal to findAll.
     *
     * @param string  $text
     * @param array   $criteria
     * @param integer $page
     * @param integer $maxPerPage
     *
     * @return array[]
     */
    public function search($text = null, array $criteria = [], int $page = 0, int $maxPerPage = 50);
}