<?php

namespace SnappMarket\Gopi\Dto;

class ProductSearchDTO
{

    /** @var string */
    private $barcode;

    /** @var string */
    private $title;

    /** @var int */
    private $page;

    /** @var int */
    private $limit;


    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): ProductSearchDTO
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): ProductSearchDTO
    {
        $this->title = $title;
        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): ProductSearchDTO
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): ProductSearchDTO
    {
        $this->limit = $limit;
        return $this;
    }
}
