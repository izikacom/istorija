<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 14:00
 */

namespace DayUse\Istorija\ReadModel;


interface BulkableInterface
{
    /**
     * @param IdentifiableValue[] $models
     */
    public function saveBulk(array $models);
}