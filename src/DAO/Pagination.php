<?php

namespace Dayuse\Istorija\DAO;


use Dayuse\Istorija\Utils\Ensure;

class Pagination
{
    public const FIRST_PAGE = 1;

    private $page;
    private $maxPerPage;

    public function __construct(int $page, int $maxPerPage)
    {
        Ensure::min($page, 1);
        Ensure::min($maxPerPage, 1);

        $this->page = $page;
        $this->maxPerPage = $maxPerPage;
    }

    public static function firstPage(int $maxPerPage = 50): self
    {
        return new self(self::FIRST_PAGE, $maxPerPage);
    }

    public static function firstLargePage(int $maxPerPage = 1000): self
    {
        return new self(self::FIRST_PAGE, $maxPerPage);
    }

    public function nextPage(): self
    {
        return new self($this->page + 1, $this->maxPerPage);
    }

    public function getOffset(): int
    {
        return $this->page - 1;
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
