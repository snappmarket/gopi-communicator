<?php

namespace SnappMarket\Gopi\Exceptions;

use Exception;

class InventoryRequestValidationException extends Exception
{
    public function __construct(?string $message = null)
    {
        if (empty($message)) {
            $message = 'خطای نامشخصی رخ داده است.';
        }
        parent::__construct($message, 422);
    }
}
