<?php

namespace Dayuse\Istorija\Utils;


/**
 * @author : Thomas Tourlourat <thomas@tourlourat.com>
 */
interface StateInterface
{
    public function get(string $key, $default = null);
    public function set(string $key, $value): StateInterface;
    public function all(): array;
    public function merge(array $data): StateInterface;
    public function copy(): StateInterface;
    public function isEmpty(): bool;

    public static function createFromState(StateInterface $state): StateInterface;
    public static function createEmpty(): StateInterface;
    public static function createFromArray(array $data): StateInterface;
}