<?php

namespace SnappMarket\Gopi\Dto;

class ProductDetailDTO
{

    /** @var string */
    private $barcode;

    /** @var int */
    private $vendorId;


    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): ProductDetailDTO
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function setVendorId(int $vendorId): ProductDetailDTO
    {
        $this->vendorId = $vendorId;
        return $this;
    }
}
