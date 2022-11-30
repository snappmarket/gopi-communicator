<?php

namespace SnappMarket\Gopi\Exceptions;

use Exception;

class InventoryNotFoundExcpetion extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }
}
