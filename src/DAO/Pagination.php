<?php

namespace Dayuse\Istorija\DAO;


class Pagination
{
    private $page;
    private $maxPerPage;

    public function __construct(int $page, int $maxPerPage)
    {
        $this->page       = $page;
        $this->maxPerPage = $maxPerPage;
    }

    public static function firstPage(int $maxPerPage = 50): self
    {
        return new self(0, $maxPerPage);
    }

    public static function firstLargePage(int $maxPerPage = 1000): self
    {
        return new self(0, $maxPerPage);
    }

    public function nextPage(): self
    {
        return new self($this->page + 1, $this->maxPerPage);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }
}