<?php

namespace SnappMarket\Gopi\Exceptions;

use Exception;

class InventoryNotFoundException extends Exception
{
    public function __construct(?string $message = null)
    {
        if (empty($message)) {
            $message = '';
        }
        parent::__construct($message, 404);
    }
}
