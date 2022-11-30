<?php

namespace SnappMarket\Gopi\Dto;

class ReplenishProductDTO
{

    /** @var int */
    private $quantity;

    /** @var int */
    private $userId;

    /** @var int */
    private $palletBatchId;

    /** @var int */
    private $palletBatchProductId;


    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): ReplenishProductDTO
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): ReplenishProductDTO
    {
        $this->userId = $userId;
        return $this;
    }

    public function getPalletBatchId(): int
    {
        return $this->palletBatchId;
    }

    public function setPalletBatchId(int $palletBatchId): ReplenishProductDTO
    {
        $this->palletBatchId = $palletBatchId;
        return $this;
    }

    public function getPalletBatchProductId(): int
    {
        return $this->palletBatchProductId;
    }

    public function setPalletBatchProductId(int $palletBatchProductId): ReplenishProductDTO
    {
        $this->palletBatchProductId = $palletBatchProductId;
        return $this;
    }
}
