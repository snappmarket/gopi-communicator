<?php

namespace SnappMarket\Gopi\Dto;

class DeductionProductDTO
{
    /** @var string */
    private $barcode;

    /** @var int */
    private $reasonId;

    /** @var int */
    private $stocks;

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): DeductionProductDTO
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getReasonId(): int
    {
        return $this->reasonId;
    }

    public function setReasonId(int $reasonId): DeductionProductDTO
    {
        $this->reasonId = $reasonId;
        return $this;
    }

    public function getStocks(): int
    {
        return $this->stocks;
    }

    public function setStocks(int $stocks): DeductionProductDTO
    {
        $this->stocks = $stocks;
        return $this;
    }
}
