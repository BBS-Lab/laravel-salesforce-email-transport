<?php

declare(strict_types=1);

namespace BBSLab\SalesforceEmailTransport\Exceptions;

use Exception;

class TokenException extends Exception
{
    public static function missingToken(): static
    {
        return new static("Unable to retrieve token.");
    }
}
