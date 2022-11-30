<?php

namespace SnappMarket\Gopi\Dto;

class DeductionDTO
{
    /** @var int */
    private $userId;

    /** @var int */
    private $vendorId;

    /** @var DeductionProductDTO[] */
    private $products;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): DeductionDTO
    {
        $this->userId = $userId;
        return $this;
    }

    public function getVendorId(): int
    {
        return $this->vendorId;
    }

    public function setVendorId(int $vendorId): DeductionDTO
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
     * @param DeductionProductDTO[] $products
     */
    public function setProducts(array $products): DeductionDTO
    {
        $this->products = $products;
        return $this;
    }

    public function addProduct(DeductionProductDTO $deductionProductDTO): DeductionDTO
    {
        $this->products[] = $deductionProductDTO;
        return $this;
    }

}
