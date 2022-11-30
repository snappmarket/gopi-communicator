<?php

namespace SnappMarket\Gopi\Dto;

class CreatePalletBatchDTO
{
    /** @var string */
    private $palletBarcode;

    /** @var int */
    private $userId;

    /** @var int */
    private $vendorId;

    /** @var CreatePalletBatchProductDTO[] */
    private $products = [];

    public function getPalletBarcode(): string
    {
        return $this->palletBarcode;
    }

    public function setPalletBarcode(string $palletBarcode): CreatePalletBatchDTO
    {
        $this->palletBarcode = $palletBarcode;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): CreatePalletBatchDTO
    {
        $this->userId = $userId;
        return $this;
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function setVendorId(int $vendorId): CreatePalletBatchDTO
    {
        $this->vendorId = $vendorId;
        return $this;
    }

    /**
     * @return DeductionProductDTO[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param CreatePalletBatchProductDTO[] $products
     */
    public function setProducts(array $products): CreatePalletBatchDTO
    {
        $this->products = $products;
        return $this;
    }

    public function addProduct(CreatePalletBatchProductDTO $deductionProductDTO): CreatePalletBatchDTO
    {
        $this->products[] = $deductionProductDTO;
        return $this;
    }

}
