<?php
/**
 * Created by PhpStorm.
 * User: thomastourlourat
 * Date: 30/08/2016
 * Time: 13:24
 */

namespace DayUse\Istorija\ReadModel;


interface DAOInterface
{
    /**
     * @param string   $id
     * @param callable $updateMethod
     * @param bool     $allowCreation
     */
    public function update(string $id, callable $updateMethod, bool $allowCreation = true);

    /**
     * @param string $id
     * @param array  $data
     */
    public function save(string $id, array $data);

    /**
     * @param string $id
     *
     * @return array|null
     */
    public function find(string $id);

    /**
     * @param string $id
     */
    public function remove(string $id);

    public function flush();
}