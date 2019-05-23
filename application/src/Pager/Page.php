<?php

namespace App\Pager;

class Page
{
    /** @var int */
    protected $currentPage;

    /** @var int */
    protected $totalPages;

    /** @var int */
    protected $totalItems;

    /** @var int */
    protected $perPage;

    /** @var array */
    protected $results;

    /**
     * Page constructor.
     *
     * @param array $results
     * @param int   $perPage
     * @param int   $totalItems
     * @param int   $currentPage
     */
    public function __construct(array $results = array(), $perPage = 10, $totalItems = 1, $currentPage = 1)
    {
        $this->results = $results;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->totalItems = $totalItems;
        $this->totalPages = (int) ceil($this->totalItems / $this->perPage);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
