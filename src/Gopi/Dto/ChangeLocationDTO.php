<?php

namespace SnappMarket\Gopi\Dto;

class ChangeLocationDTO
{
    /** @var int */
    private $userId;

    /** @var int */
    private $vendorId;

    /** @var string */
    private $productBarcode;

    /** @var string */
    private $currentAisleBarcode;

    /** @var string */
    private $newAisleBarcode;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): ChangeLocationDTO
    {
        $this->userId = $userId;
        return $this;
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function setVendorId(int $vendorId): ChangeLocationDTO
    {
        $this->vendorId = $vendorId;
        return $this;
    }

    public function getProductBarcode(): string
    {
        return $this->productBarcode;
    }

    public function setProductBarcode(string $productBarcode): ChangeLocationDTO
    {
        $this->productBarcode = $productBarcode;
        return $this;
    }

    public function getCurrentAisleBarcode(): string
    {
        return $this->currentAisleBarcode;
    }

    public function setCurrentAisleBarcode(string $currentAisleBarcode): ChangeLocationDTO
    {
        $this->currentAisleBarcode = $currentAisleBarcode;
        return $this;
    }

    public function getNewAisleBarcode(): string
    {
        return $this->newAisleBarcode;
    }

    public function setNewAisleBarcode(string $newAisleBarcode): ChangeLocationDTO
    {
        $this->newAisleBarcode = $newAisleBarcode;
        return $this;
    }

}