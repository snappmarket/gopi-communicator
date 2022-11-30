<?php

namespace SnappMarket\Gopi\Dto;

class CheckBarcodeDTO
{

    /** @var string */
    private $barcode;

    /** @var int */
    private $vendorId;

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode(string $barcode): void
    {
        $this->barcode = $barcode;
    }

    /**
     * @return int
     */
    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    /**
     * @param int $vendorId
     */
    public function setVendorId(int $vendorId): void
    {
        $this->vendorId = $vendorId;
    }
}
