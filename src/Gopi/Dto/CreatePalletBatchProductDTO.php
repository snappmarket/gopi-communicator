<?php

namespace SnappMarket\Gopi\Dto;

class CreatePalletBatchProductDTO
{
    /** @var string */
    private $barcode;

    /** @var int */
    private $quantity;

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): CreatePalletBatchProductDTO
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): CreatePalletBatchProductDTO
    {
        $this->quantity = $quantity;
        return $this;
    }
}
