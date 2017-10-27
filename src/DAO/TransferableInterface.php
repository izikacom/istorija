<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:00
 */

namespace Dayuse\Istorija\DAO;


interface TransferableInterface
{
    public function transferTo(DAOInterface $otherDAO);
}